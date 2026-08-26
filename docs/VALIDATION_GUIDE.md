# FluentValidation Guide

## Overview

This project uses **FluentValidation** for robust input validation. Validation happens automatically on all API requests before reaching controller actions.

## Architecture

```
HTTP Request → Model Binding → FluentValidation → Controller Action
                                      ↓ (if invalid)
                                400 Bad Request
```

## Benefits

✅ **Automatic Validation**: No manual `ModelState.IsValid` checks needed
✅ **Centralized Rules**: All validation logic in one place
✅ **Reusable**: Validators can be used across multiple endpoints
✅ **Testable**: Easy to unit test validation rules
✅ **Clear Error Messages**: Descriptive, user-friendly error messages

## Existing Validators

### Authentication Validators
- `RegisterRequestValidator` - User registration validation
- `LoginRequestValidator` - Login credential validation
- `ForgotPasswordRequestValidator` - Password reset request validation
- `ResetPasswordRequestValidator` - Password reset validation
- `ChangePasswordRequestValidator` - Password change validation

### Admission Validators
- `BulkAdmissionImportDtoValidator` - Bulk import validation (for 200K+ records)
- `AdmissionQueryDtoValidator` - Search/filter query validation

### College Validators
- `CollegeRequestValidator` - College creation/update validation
- `ProgramRequestValidator` - Program creation validation
- `DistrictRequestValidator` - District creation validation

## Validation Rules Summary

### Password Requirements
- Minimum 8 characters
- At least one uppercase letter (A-Z)
- At least one lowercase letter (a-z)
- At least one number (0-9)
- At least one special character (@$!%*?&#)

### CNIC Format
- Must be in format: `12345-1234567-1`
- Example: `35202-8765432-1`

### Phone Number Format
- Valid Pakistani phone number
- Accepts formats:
  - `+923001234567`
  - `03001234567`
  - `923001234567`

### Email
- Must be valid email format
- Maximum 200 characters

### Names
- Only letters, spaces, hyphens, apostrophes, and periods
- No numbers or special characters (except - ' .)

### Age Restrictions
- Students must be at least 15 years old
- Date of birth must be realistic (not more than 100 years ago)

## How Validation Works

### Automatic Validation

When a request comes in, FluentValidation automatically validates it:

```csharp
[HttpPost("register")]
public async Task<IActionResult> Register([FromBody] RegisterRequest request)
{
    // No need to check ModelState.IsValid!
    // If validation fails, FluentValidation returns 400 automatically
    
    // Your code here...
}
```

### Validation Error Response

If validation fails, the API returns a `400 Bad Request` with detailed errors:

```json
{
  "type": "https://tools.ietf.org/html/rfc7231#section-6.5.1",
  "title": "One or more validation errors occurred.",
  "status": 400,
  "errors": {
    "Email": ["Invalid email format"],
    "Password": [
      "Password must be at least 8 characters",
      "Password must contain at least one uppercase letter"
    ],
    "CNIC": ["CNIC must be in format: 12345-1234567-1"]
  }
}
```

## Creating New Validators

### Step 1: Create Validator Class

```csharp
using FluentValidation;

namespace SaluExamPortal.YourNamespace.Validators
{
    public class YourRequestValidator : AbstractValidator<YourRequest>
    {
        public YourRequestValidator()
        {
            RuleFor(x => x.PropertyName)
                .NotEmpty().WithMessage("Property is required")
                .MaximumLength(100).WithMessage("Cannot exceed 100 characters");
                
            RuleFor(x => x.Email)
                .EmailAddress().WithMessage("Invalid email format");
                
            RuleFor(x => x.Age)
                .GreaterThanOrEqualTo(18).WithMessage("Must be 18 or older");
        }
    }
}
```

### Step 2: No Additional Configuration Needed!

The validator is automatically discovered and registered because we use:
```csharp
builder.Services.AddValidatorsFromAssemblyContaining<Program>();
```

## Common Validation Rules

### Required Fields
```csharp
RuleFor(x => x.Name)
    .NotEmpty().WithMessage("Name is required");
```

### String Length
```csharp
RuleFor(x => x.Name)
    .MinimumLength(3).WithMessage("Must be at least 3 characters")
    .MaximumLength(100).WithMessage("Cannot exceed 100 characters");
```

### Email
```csharp
RuleFor(x => x.Email)
    .EmailAddress().WithMessage("Invalid email format");
```

### Regex Pattern
```csharp
RuleFor(x => x.CNIC)
    .Matches(@"^\d{5}-\d{7}-\d{1}$")
    .WithMessage("CNIC must be in format: 12345-1234567-1");
```

### Number Range
```csharp
RuleFor(x => x.Age)
    .InclusiveBetween(15, 100)
    .WithMessage("Age must be between 15 and 100");
```

### Date Validation
```csharp
RuleFor(x => x.DateOfBirth)
    .LessThan(DateTime.Today)
    .WithMessage("Date of birth must be in the past");
```

### Conditional Validation
```csharp
RuleFor(x => x.PhoneNumber)
    .NotEmpty()
    .When(x => x.ContactMethod == "PHONE")
    .WithMessage("Phone number is required when contact method is PHONE");
```

### Enum Validation
```csharp
RuleFor(x => x.Status)
    .Must(s => new[] { "ACTIVE", "INACTIVE", "PENDING" }.Contains(s))
    .WithMessage("Invalid status value");
```

### Custom Validation
```csharp
RuleFor(x => x.StartDate)
    .Must((model, startDate) => startDate < model.EndDate)
    .WithMessage("Start date must be before end date");
```

### Nested Object Validation
```csharp
RuleFor(x => x.Address)
    .SetValidator(new AddressValidator());
```

### Collection Validation
```csharp
RuleForEach(x => x.Items)
    .SetValidator(new ItemValidator());

RuleFor(x => x.Items)
    .Must(x => x.Count > 0)
    .WithMessage("At least one item is required");
```

## Testing Validators

Validators are easy to unit test:

```csharp
[Fact]
public void Should_Have_Error_When_Email_Is_Empty()
{
    var validator = new RegisterRequestValidator();
    var model = new RegisterRequest { Email = "" };
    
    var result = validator.Validate(model);
    
    result.Errors.Should().Contain(x => x.PropertyName == "Email");
}

[Fact]
public void Should_Not_Have_Error_When_Email_Is_Valid()
{
    var validator = new RegisterRequestValidator();
    var model = new RegisterRequest { Email = "test@example.com" };
    
    var result = validator.TestValidate(model);
    
    result.ShouldNotHaveValidationErrorFor(x => x.Email);
}
```

## Manual Validation (When Needed)

In rare cases where you need manual validation:

```csharp
public class MyService
{
    private readonly IValidator<MyModel> _validator;
    
    public MyService(IValidator<MyModel> validator)
    {
        _validator = validator;
    }
    
    public async Task ProcessAsync(MyModel model)
    {
        var validationResult = await _validator.ValidateAsync(model);
        
        if (!validationResult.IsValid)
        {
            throw new ValidationException(validationResult.Errors);
        }
        
        // Process valid model...
    }
}
```

## Performance Tips

### 1. Async Validation
Use `MustAsync` for async rules:
```csharp
RuleFor(x => x.Email)
    .MustAsync(async (email, cancellation) => 
    {
        return await _userRepository.IsEmailUniqueAsync(email);
    })
    .WithMessage("Email already exists");
```

### 2. Fail Fast
Stop on first failure for better performance with large datasets:
```csharp
RuleFor(x => x.Items)
    .Cascade(CascadeMode.Stop)
    .NotEmpty()
    .Must(x => x.Count <= 10000);
```

### 3. Precompile Regex
For frequently used patterns:
```csharp
private static readonly Regex CnicRegex = new Regex(@"^\d{5}-\d{7}-\d{1}$", RegexOptions.Compiled);

RuleFor(x => x.CNIC)
    .Must(cnic => CnicRegex.IsMatch(cnic));
```

## Best Practices

✅ **DO:**
- Use descriptive error messages
- Validate at the API boundary (DTOs)
- Keep validators simple and focused
- Test your validators
- Use built-in rules when available

❌ **DON'T:**
- Put business logic in validators (validators should only check format/constraints)
- Validate domain entities (validate DTOs instead)
- Make validators dependent on external services (except for uniqueness checks)
- Use validators for authorization checks

## Common Mistakes

### Mistake 1: Forgetting to Add Validator
**Problem:** Created validator but it's not being used

**Solution:** Validators are auto-discovered. Make sure:
1. Class name ends with `Validator`
2. Inherits from `AbstractValidator<T>`
3. Is in the same assembly as Program.cs

### Mistake 2: Wrong Error Message Property
**Problem:** Error shows on wrong field

**Solution:** Check RuleFor selector:
```csharp
// Wrong
RuleFor(x => x).Must(x => x.StartDate < x.EndDate);

// Correct
RuleFor(x => x.StartDate)
    .LessThan(x => x.EndDate);
```

### Mistake 3: Overcomplicating Validators
**Problem:** Validator contains business logic

**Solution:** Keep validators simple:
```csharp
// Wrong - business logic in validator
RuleFor(x => x)
    .Must(x => CalculateComplexBusinessRule(x));

// Correct - simple format validation
RuleFor(x => x.Amount)
    .GreaterThan(0);
```

## Summary

- ✅ FluentValidation is configured and ready to use
- ✅ Validators are automatically discovered and applied
- ✅ Validation errors return structured 400 responses
- ✅ Add new validators by creating classes that inherit `AbstractValidator<T>`
- ✅ No manual ModelState checks needed
