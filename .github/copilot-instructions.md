# Copilot Instructions

Project: Laravel E-commerce (Nội thất)

## Coding Rules
- Use Laravel Eloquent ORM
- Use Service layer for business logic
- Use FormRequest for validation
- Use Resource for API response

## Architecture
- Controller: handle request/response only
- Service: business logic
- Repository: database query

## Naming Convention
- Use meaningful variable names
- Function name must describe action

## Database
- products
- categories
- orders
- order_details
- users

## Business Rules
- Check stock before creating order
- Use DB transaction when creating order
- Calculate total_price from order_details

## Response
- Return JSON for API
- Use pagination for list

## Code Style
- Follow PSR-12