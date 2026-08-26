# Repository Pattern & Unit of Work Guide

## Overview

This project uses the **Repository Pattern** with **Unit of Work** for data access. This provides:
- Centralized transaction management
- Clean separation of concerns
- Testable data access layer
- Optimized bulk operations for large datasets

## Architecture

```
Controller → Service → UnitOfWork → Repository → DbContext
```

## Key Principles

### 1. **Repository Does NOT Call SaveChanges**
Individual repository methods (Add, Update, Delete) **track changes** but don't save them.

### 2. **UnitOfWork Coordinates Saves**
All `SaveChangesAsync()` calls should go through `UnitOfWork` to maintain transaction boundaries.

### 3. **Bulk Operations Are Self-Contained**
Bulk operations (BulkInsert, BulkUpdate, BulkDelete) auto-save for performance reasons.

## Usage Examples

### ✅ Correct Pattern

```csharp
public class AdmissionService
{
    private readonly IUnitOfWork _unitOfWork;

    public async Task<Enrollment> CreateEnrollment(EnrollmentDto dto)
    {
        var enrollment = new Enrollment { /* ... */ };
        
        // Add to repository (tracks change)
        await _unitOfWork.Repository<Enrollment>().AddAsync(enrollment);
        
        // Save through UnitOfWork
        await _unitOfWork.SaveChangesAsync();
        
        return enrollment;
    }

    public async Task UpdateMultipleRecords(List<UpdateDto> updates)
    {
        // Start transaction for multiple operations
        await _unitOfWork.BeginTransactionAsync();
        
        try
        {
            foreach (var update in updates)
            {
                var entity = await _unitOfWork.Repository<Enrollment>()
                    .GetByIdAsync(update.Id);
                
                entity.Status = update.Status;
                await _unitOfWork.Repository<Enrollment>().UpdateAsync(entity);
            }
            
            // Single save for all changes
            await _unitOfWork.CommitAsync();
        }
        catch
        {
            await _unitOfWork.RollbackAsync();
            throw;
        }
    }
}
```

### ❌ Incorrect Pattern (Old Way)

```csharp
// DON'T DO THIS - Repository shouldn't save
public async Task<Enrollment> CreateEnrollment(EnrollmentDto dto)
{
    var enrollment = new Enrollment { /* ... */ };
    await repository.AddAsync(enrollment);
    await repository.SaveChangesAsync(); // ❌ Wrong!
    return enrollment;
}
```

## Transaction Management

### Simple Operations (Single Save)
```csharp
await _unitOfWork.Repository<Entity>().AddAsync(entity);
await _unitOfWork.SaveChangesAsync();
```

### Complex Operations (Transaction)
```csharp
await _unitOfWork.BeginTransactionAsync();
try
{
    // Multiple operations
    await _unitOfWork.Repository<Entity1>().AddAsync(entity1);
    await _unitOfWork.Repository<Entity2>().UpdateAsync(entity2);
    
    // Commit all or nothing
    await _unitOfWork.CommitAsync();
}
catch
{
    await _unitOfWork.RollbackAsync();
    throw;
}
```

## Bulk Operations

For large datasets (200,000+ records), use bulk operations:

```csharp
// Bulk operations auto-save
var admissions = /* ... large list ... */;
await _unitOfWork.Repository<Enrollment>()
    .BulkInsertAsync(admissions, batchSize: 1000);

// No need to call SaveChangesAsync after bulk operations
```

## Performance Tips

### 1. Use AsNoTracking for Read-Only Queries
```csharp
// Add this to repository for read-only operations
public async Task<IEnumerable<T>> GetAllReadOnlyAsync()
{
    return await _dbSet.AsNoTracking().ToListAsync();
}
```

### 2. Batch Multiple Changes
```csharp
// Better: One save for multiple changes
await repo.AddAsync(entity1);
await repo.AddAsync(entity2);
await repo.AddAsync(entity3);
await _unitOfWork.SaveChangesAsync(); // One save

// Avoid: Multiple saves
await repo.AddAsync(entity1);
await _unitOfWork.SaveChangesAsync(); // Save 1
await repo.AddAsync(entity2);
await _unitOfWork.SaveChangesAsync(); // Save 2
```

### 3. Use Bulk Operations for Large Datasets
- 1-100 records: Use regular Add/Update/Delete
- 100-1000 records: Use AddRange/UpdateRange/DeleteRange
- 1000+ records: Use BulkInsert/BulkUpdate/BulkDelete

## Migration Notes

If you have existing code that calls `repository.SaveChangesAsync()` directly:

1. Remove the `SaveChangesAsync()` call from the repository method
2. Add `await _unitOfWork.SaveChangesAsync()` in the calling service
3. Consider if the operation needs a transaction

## Common Mistakes

### Mistake 1: Forgetting to Save
```csharp
await _unitOfWork.Repository<Entity>().UpdateAsync(entity);
// ❌ Forgot to save - changes won't persist!
```

**Fix:**
```csharp
await _unitOfWork.Repository<Entity>().UpdateAsync(entity);
await _unitOfWork.SaveChangesAsync(); // ✅ Now it saves
```

### Mistake 2: Multiple Saves in Loop
```csharp
foreach (var item in items)
{
    await repo.AddAsync(item);
    await _unitOfWork.SaveChangesAsync(); // ❌ Slow! Saves each item
}
```

**Fix:**
```csharp
foreach (var item in items)
{
    await repo.AddAsync(item);
}
await _unitOfWork.SaveChangesAsync(); // ✅ One save for all items
```

### Mistake 3: Not Using Transactions
```csharp
await repo1.AddAsync(entity1);
await _unitOfWork.SaveChangesAsync();
await repo2.AddAsync(entity2);
await _unitOfWork.SaveChangesAsync();
// ❌ If second save fails, first save already committed
```

**Fix:**
```csharp
await _unitOfWork.BeginTransactionAsync();
try
{
    await repo1.AddAsync(entity1);
    await repo2.AddAsync(entity2);
    await _unitOfWork.CommitAsync(); // ✅ Both succeed or both fail
}
catch
{
    await _unitOfWork.RollbackAsync();
    throw;
}
```

## Summary

✅ **DO:**
- Use `UnitOfWork.SaveChangesAsync()` for all saves
- Use transactions for multi-step operations
- Use bulk operations for large datasets
- Batch multiple changes before saving

❌ **DON'T:**
- Call `repository.SaveChangesAsync()` in service code (use UnitOfWork)
- Save after every single change
- Mix regular operations with bulk operations in the same transaction
