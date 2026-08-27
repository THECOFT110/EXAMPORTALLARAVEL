# Performance Optimization Guide

## Overview

This project is optimized for handling large datasets (200,000+ student records) with response caching, query optimization, and bulk operations.

## Response Caching Strategy

### Output Cache Policies

The application uses **Output Cache** for API responses with the following policies:

| Policy | Duration | Use Case | Tag |
|--------|----------|----------|-----|
| `default` | 5 minutes | General API endpoints | `default` |
| `statistics` | 10 minutes | Dashboard statistics | `statistics` |
| `lookup` | 1 hour | Reference data (districts, colleges, programs) | `lookup` |
| `admissions` | 2 minutes | Admission lists (frequently changing) | `admissions` |

### Applying Cache to Endpoints

```csharp
[HttpGet("statistics/{academicYearId}")]
[OutputCache(PolicyName = "statistics")]
public async Task<ActionResult<AdmissionStatisticsDto>> GetStatistics(Guid academicYearId)
{
    // This response will be cached for 10 minutes
    var stats = await _admissionService.GetStatisticsAsync(academicYearId);
    return Ok(stats);
}

[HttpGet("colleges")]
[OutputCache(PolicyName = "lookup")]
public async Task<ActionResult<List<College>>> GetColleges()
{
    // This response will be cached for 1 hour
    var colleges = await _unitOfWork.Repository<College>().GetAllReadOnlyAsync();
    return Ok(colleges);
}
```

### Cache Invalidation

Use tags to invalidate cache when data changes:

```csharp
private readonly IOutputCacheStore _cacheStore;

public async Task UpdateAdmission(Guid id, UpdateDto dto)
{
    // Update logic...
    await _unitOfWork.SaveChangesAsync();
    
    // Invalidate cached admission lists
    await _cacheStore.EvictByTagAsync("admissions", cancellationToken);
}
```

## Query Optimization

### AsNoTracking for Read-Only Queries

**Problem:** EF Core tracks all entities by default, using memory and CPU
**Solution:** Use `AsNoTracking()` for read-only queries

```csharp
// ❌ BAD - Tracks entities (slower, uses more memory)
var admissions = await _dbContext.Enrollments.ToListAsync();

// ✅ GOOD - No tracking (faster, less memory)
var admissions = await _dbContext.Enrollments.AsNoTracking().ToListAsync();
```

### Repository Methods

The repository provides optimized read-only methods:

```csharp
// Standard methods (with tracking - use when you'll modify entities)
await repository.GetAllAsync();
await repository.FindAsync(x => x.Status == "PENDING");
await repository.GetPagedAsync(1, 50);

// Read-only methods (no tracking - use for display/reporting)
await repository.GetAllReadOnlyAsync();
await repository.FindReadOnlyAsync(x => x.Status == "PENDING");
await repository.GetPagedReadOnlyAsync(1, 50);
```

### When to Use Each

| Scenario | Method | Reason |
|----------|--------|--------|
| Displaying a list | `GetAllReadOnlyAsync()` | No modifications needed |
| Getting data for a report | `FindReadOnlyAsync()` | Read-only operation |
| Pagination for UI | `GetPagedReadOnlyAsync()` | Display only |
| Loading entity to update | `GetByIdAsync()` | Need change tracking |
| Loading entity to delete | `FindAsync()` | Need change tracking |

## Database Query Optimization

### Select Only Needed Columns

```csharp
// ❌ BAD - Loads all columns
var users = await _dbContext.Users
    .Where(x => x.Role == "STUDENT")
    .ToListAsync();

// ✅ GOOD - Loads only needed columns
var users = await _dbContext.Users
    .Where(x => x.Role == "STUDENT")
    .Select(x => new { x.Id, x.FullName, x.Email })
    .ToListAsync();
```

### Eager Loading for Related Data

```csharp
// ❌ BAD - N+1 query problem
var enrollments = await _dbContext.Enrollments.ToListAsync();
foreach (var enrollment in enrollments)
{
    var college = await _dbContext.Colleges.FindAsync(enrollment.CollegeId);
    // This runs a query for EACH enrollment!
}

// ✅ GOOD - Single query with JOIN
var enrollments = await _dbContext.Enrollments
    .Include(e => e.College)
    .Include(e => e.Program)
    .Include(e => e.User)
    .AsNoTracking()
    .ToListAsync();
```

### Filtered Includes

```csharp
// ✅ Load only active programs for each college
var colleges = await _dbContext.Colleges
    .Include(c => c.Programs.Where(p => p.IsActive))
    .AsNoTracking()
    .ToListAsync();
```

### Pagination (Required for Large Datasets)

```csharp
// ❌ BAD - Loads all 200,000 records!
var allAdmissions = await _dbContext.Enrollments.ToListAsync();

// ✅ GOOD - Loads only requested page
var page = 1;
var pageSize = 50;
var admissions = await _dbContext.Enrollments
    .AsNoTracking()
    .OrderBy(e => e.ApplicationDate)
    .Skip((page - 1) * pageSize)
    .Take(pageSize)
    .ToListAsync();
```

## Bulk Operations

For large-scale operations (1000+ records), use bulk methods:

```csharp
// ❌ BAD - Individual inserts (very slow for 200K records)
foreach (var admission in admissions)
{
    await repository.AddAsync(admission);
}
await _unitOfWork.SaveChangesAsync();

// ✅ GOOD - Bulk insert (100x faster)
await repository.BulkInsertAsync(admissions, batchSize: 1000);
```

### Bulk Operation Methods

```csharp
// Bulk Insert - Optimized for 200,000+ records
await _unitOfWork.Repository<Enrollment>()
    .BulkInsertAsync(enrollments, batchSize: 1000);

// Bulk Update
await _unitOfWork.Repository<Enrollment>()
    .BulkUpdateAsync(enrollments, batchSize: 1000);

// Bulk Delete
await _unitOfWork.Repository<Enrollment>()
    .BulkDeleteAsync(enrollments, batchSize: 1000);
```

### Batch Size Guidelines

| Record Count | Recommended Batch Size |
|--------------|------------------------|
| < 1,000 | 500 |
| 1,000 - 10,000 | 1,000 |
| 10,000 - 100,000 | 2,000 |
| 100,000+ | 5,000 |

## Async Operations

Always use async methods for database operations:

```csharp
// ❌ BAD - Blocks thread
var users = repository.GetAll();

// ✅ GOOD - Non-blocking
var users = await repository.GetAllAsync();
```

## Memory Management

### Dispose Resources

```csharp
// ✅ Using statement ensures disposal
using (var scope = serviceProvider.CreateScope())
{
    var dbContext = scope.ServiceProvider.GetService<ApplicationDbContext>();
    // Use dbContext...
}
// Automatically disposed here
```

### Stream Large Results

For very large result sets:

```csharp
// ✅ Stream results instead of loading all at once
await foreach (var enrollment in _dbContext.Enrollments.AsAsyncEnumerable())
{
    // Process one at a time
    await ProcessEnrollmentAsync(enrollment);
}
```

## Index Optimization

Proper indexes dramatically improve query performance. See the next section on database indexes.

## Compression

Enable response compression for large JSON payloads:

```csharp
// In Program.cs (already configured)
builder.Services.AddResponseCompression(options =>
{
    options.EnableForHttps = true;
    options.Providers.Add<GzipCompressionProvider>();
});
```

## Connection Pooling

EF Core automatically pools connections. Recommended settings:

```json
"ConnectionStrings": {
  "DefaultConnection": "Server=...;Database=...;Max Pool Size=100;Min Pool Size=5;..."
}
```

## Performance Monitoring

### Logging Slow Queries

EF Core logs slow queries automatically with Serilog:

```csharp
// Check logs for queries taking > 100ms
// logs/log-YYYYMMDD.txt
```

### Query Statistics

```csharp
// Enable in development
if (app.Environment.IsDevelopment())
{
    app.UseMiddleware<QueryStatisticsMiddleware>();
}
```

## Performance Checklist

### For Every Query
- [ ] Use `AsNoTracking()` for read-only operations
- [ ] Select only needed columns
- [ ] Use eager loading (`Include()`) to avoid N+1 queries
- [ ] Implement pagination for large result sets
- [ ] Add appropriate indexes

### For Every Endpoint
- [ ] Add output caching where appropriate
- [ ] Use async/await consistently
- [ ] Return minimal DTOs (not full entities)
- [ ] Implement rate limiting for expensive operations

### For Bulk Operations
- [ ] Use `BulkInsertAsync/BulkUpdateAsync/BulkDeleteAsync` for > 1000 records
- [ ] Choose appropriate batch size
- [ ] Process in background job for > 10,000 records
- [ ] Provide progress updates to users

## Performance Testing

### Load Testing Recommendations

Test with realistic data volumes:

```bash
# Test with 200,000 admission records
# Target metrics:
# - List endpoint: < 200ms
# - Search endpoint: < 500ms
# - Bulk import: < 60 seconds per 10,000 records
```

## Common Performance Issues

### Issue 1: Slow List Pages
**Symptom:** List pages take 5+ seconds to load

**Solution:**
```csharp
// Use AsNoTracking + pagination
var result = await _unitOfWork.Repository<Enrollment>()
    .GetPagedReadOnlyAsync(pageNumber, pageSize, 
        predicate: x => x.Status == "PENDING");
```

### Issue 2: N+1 Query Problem
**Symptom:** One query per row in logs

**Solution:**
```csharp
// Use Include() for related data
var enrollments = await _dbContext.Enrollments
    .Include(e => e.College)
    .Include(e => e.Program)
    .AsNoTracking()
    .ToListAsync();
```

### Issue 3: Slow Bulk Imports
**Symptom:** Importing 10,000 records takes > 5 minutes

**Solution:**
```csharp
// Use bulk operations
await repository.BulkInsertAsync(records, batchSize: 2000);
```

### Issue 4: High Memory Usage
**Symptom:** Application uses > 2GB RAM

**Solution:**
```csharp
// Process in batches
var batches = records.Chunk(1000);
foreach (var batch in batches)
{
    await ProcessBatchAsync(batch);
}
```

## Performance Monitoring Tools

### Application Insights (Recommended for Production)
- Track response times
- Identify slow queries
- Monitor exceptions

### MiniProfiler (Development)
```csharp
// Add to see query performance in development
builder.Services.AddMiniProfiler(options =>
{
    options.RouteBasePath = "/profiler";
}).AddEntityFramework();
```

## Summary

✅ **Key Takeaways:**
- Use `AsNoTracking()` for read-only queries
- Implement pagination for large datasets
- Use bulk operations for > 1000 records
- Add output caching to frequently accessed endpoints
- Always use async/await
- Add database indexes (see next guide)
- Monitor and measure performance

⚡ **Performance Targets:**
- API response time: < 200ms (p95)
- List pages: < 500ms with pagination
- Bulk imports: < 10 seconds per 10,000 records
- Concurrent users: 500+ without degradation
