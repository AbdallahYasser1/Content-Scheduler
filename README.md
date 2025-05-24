# Posts Scheduler - Laravel Backend Challenge

A Laravel application that lets users create and schedule posts across multiple social platforms.

## Features

- User authentication with Session(Frontend) Or Laravel Sanctum (API)
- Create, read, update, and delete posts
- Efficient Filtering Approach
- Schedule posts for future publication
- Support for multiple social platforms (e.g., Twitter, Facebook, Instagram)
- Manual Publish
- Platform-specific validation (e.g., Twitter character limits and image requirements)
- Toggle active platforms for users
- Post analytics (posts per platform, success rate, scheduled vs. published counts)
- Rate limiting (max 10 scheduled posts per day)
- Activity logging for user actions
- Bootstrap UI with responsive design

## Requirements

- PHP 8.1+
- Composer
- MySql
- Node.js and NPM

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd Content-Scheduler
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install and compile frontend assets:
```bash
npm install
npm run build
```

4. Create a copy of the environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure the database in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=content-sch
DB_USERNAME=root
DB_PASSWORD=
QUEUE_CONNECTION=database # For background processing
```
7. Run migrations and seeders:
```bash
php artisan migrate
php artisan db:seed
```

8. Start the development server:
```bash
php artisan serve
php aritsan queue:work
```
9. Access the application at `http://localhost:8000`

## API Endpoints

### Authentication

- `POST /api/register` - Register a new user
- `POST /api/login` - Login and get access token
- `GET /api/profile` - Get authenticated user profile
- `PUT /api/profile` - Update user profile
- `POST /api/logout` - Logout (invalidate token)

### Posts

- `GET /api/posts` - Get user's posts (with optional filters: status, date)
- `POST /api/posts` - Create a new post
- `GET /api/posts/{id}` - Get a specific post
- `PUT /api/posts/{id}` - Update a post
- `DELETE /api/posts/{id}` - Delete a post
- `DELETE /api/posts/{id}/publish` - Publish a draft post

### Platforms

- `GET /api/platforms` - List available platforms
- `POST /api/platforms/{id}/toggle` - Toggle active status of a platform for the user

### Analytics

- `GET /api/analytics` - Get post analytics (posts per platform, success rate, counts)
- `GET /api/dashboard` - User's dashboard with analytics and recent activity

## Scheduled Posts Processing

To process scheduled posts manually (A Mocking ), run: (Make sure that there are scheduled posts in the database)

```bash
php artisan process:posts
```

This command will:
1. Find all scheduled posts that are due for publication
2. Update their status to "published"
3. Mock the publishing process to each platform
4. Update the platform status in the pivot table

## Design Choices and Trade-offs

### Database Design

- Created pivot tables for many-to-many relationships (posts-platforms, users-platforms)
- Used enum for post status to ensure data integrity

### API Design

- RESTful API with resource controllers
- Used Laravel Sanctum for API authentication
- Implemented middleware for rate limiting

### Frontend

- Used Laravel UI with Bootstrap for rapid development
- Responsive design for mobile and desktop
- Created A session based authentication for the frontend 

### Performance Considerations

- Added eager loading to reduce N+1 query problems
- Implemented pagination for listing endpoints

## Creative Features

1. **Post Analytics**: Shows posts per platform, publishing success rate, and status counts
2. **Rate Limiting**: Restricts users to max 10 scheduled posts per day
3. **Activity Logging**: Tracks user actions throughout the application
4. **Custom Feature**: Added platform-specific validation for content

