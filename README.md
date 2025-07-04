# Laravel 12 CRUD with MongoDB and Repository Pattern

This is a Laravel 12 application implementing a CRUD system for managing posts with titles, content, optional images, and tags, using MongoDB as the database and the repository pattern for data access. The application includes AJAX-based search functionality and supports a many-to-many relationship between posts and tags, stored in a `post_tag` pivot collection. Soft deletes, caching, and pagination are not included.

## Features
- **CRUD Operations**: Create, read, update, and delete posts with titles, content, images, and tags.
- **MongoDB Integration**: Uses MongoDB for schemaless storage with `mongodb/laravel-mongodb`.
- **Repository Pattern**: Abstracts database operations using a clean repository interface.
- **Tags Management**: Supports comma-separated tags with a many-to-many relationship.
- **AJAX Search**: Real-time title-based search with tag display in the posts listing.
- **Image Upload**: Optional image upload with storage in `public/posts`.

## Prerequisites
- PHP >= 8.2
- Composer
- MongoDB (local or MongoDB Atlas)
- Node.js and npm (for front-end assets)
- Laravel CLI

## Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-username/laravel-mongodb-crud.git
   cd laravel-mongodb-crud
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Configure Environment**
   - Copy `.env.example` to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Update `.env` for MongoDB:
     ```env
     DB_CONNECTION=mongodb
     MONGODB_URI=mongodb://127.0.0.1:27017
     MONGODB_DATABASE=laravel_mongodb_crud
     ```
     For MongoDB Atlas:
     ```env
     DB_CONNECTION=mongodb
     MONGODB_URI=mongodb+srv://<username>:<password>@<cluster>.mongodb.net/laravel_mongodb_crud?retryWrites=true&w=majority
     MONGODB_DATABASE=laravel_mongodb_crud
     ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations**
   ```bash
   php artisan migrate
   ```

6. **Create Storage Link for Images**
   ```bash
   php artisan storage:link
   ```

7. **Create MongoDB Text Index**
   ```javascript
   mongosh
   use laravel_mongodb_crud
   db.posts.createIndex({ title: "text" });
   ```

8. **Start MongoDB and Laravel Server**
   - Local MongoDB:
     ```bash
     mongod
     ```
   - Laravel server:
     ```bash
     php artisan serve
     ```

## Project Structure
```
app/
├── Http/
│   ├── Controllers/PostController.php       # Handles CRUD and search
│   ├── Requests/PostRequest.php            # Form validation
├── Models/
│   ├── Post.php                            # Post model with tags relationship
│   ├── Tag.php                             # Tag model
├── Repositories/
│   ├── BaseRepository.php                  # Abstract base repository
│   ├── EloquentPostRepository.php          # Post repository implementation
│   ├── PostRepositoryInterface.php         # Repository interface
│   ├── Criteria/TitleContains.php          # Search criteria
resources/views/
│   ├── posts/
│   │   ├── index.blade.php                 # Posts listing with AJAX search
│   │   ├── create.blade.php                # Create post form
│   │   ├── edit.blade.php                  # Edit post form
│   │   ├── show.blade.php                  # View post details
routes/web.php                              # Routes definition
database/migrations/                        # MongoDB collection migrations
```

## Usage
- **Access the Application**: Visit `http://127.0.0.1:8000/posts`.
- **Create Post**: Go to `/posts/create`, enter title, content, optional image, and tags (e.g., `news, tech, blog`).
- **Edit Post**: Go to `/posts/{id}/edit` to update post details and tags.
- **Search Posts**: Use the search bar on `/posts` to filter by title (AJAX).
- **View Post**: Go to `/posts/{id}` to see post details with tags.
- **Delete Post**: Delete posts from the index page, which also detaches tags.
- **Remove Image**: Remove images from the edit page.

## Routes
```bash
php artisan route:list
```
Output (partial):
```
GET|HEAD  / ............................................................. Closure
GET|HEAD  posts ........................................ posts.index › PostController@index
POST      posts ........................................ posts.store › PostController@store
GET|HEAD  posts/create ................................ posts.create › PostController@create
POST      posts/search ................................ posts.search › PostController@search
GET|HEAD  posts/{post} ................................ posts.show › PostController@show
GET|HEAD  posts/{post}/edit ........................... posts.edit › PostController@edit
PUT|PATCH posts/{post} ........................... posts.update › PostController@update
DELETE    posts/{post} ........................... posts.destroy › PostController@destroy
DELETE    posts/{id}/image ............. posts.removeImage › PostController@removeImage
```

## Testing
1. **Start MongoDB**:
   ```bash
   mongod
   ```
2. **Run Server**:
   ```bash
   php artisan serve
   ```
3. **Test CRUD**:
   - Create a post with tags at `/posts/create`.
   - Verify tags in the `post_tag` collection:
     ```javascript
     mongosh
     use laravel_mongodb_crud
     db.post_tag.find()
     ```
   - Search posts by title and confirm tags display in the table.
   - Update or delete posts and verify tag synchronization.
4. **Check Logs**:
   - View `storage/logs/laravel.log` for errors.
   - Use browser developer tools (F12) to inspect AJAX responses.

## Troubleshooting
- **Tags Not Displaying**: Ensure `$posts->load('tags')` is in the `index` and `search` methods of `PostController`.
- **MongoDB Connection Issues**: Verify `MONGODB_URI` in `.env` and ensure MongoDB is running.
- **Search Not Working**: Check the text index (`db.posts.createIndex({ title: "text" })`) and AJAX script in `index.blade.php`.
- **Image Upload Issues**: Confirm `storage:link` is run and the `public` disk is configured in `config/filesystems.php`.

## Dependencies
- `laravel/framework`: ^12.0
- `mongodb/laravel-mongodb`: ^5.1
- `jquery`: 3.6.0 (via CDN)
- Bootstrap 5 (for styling)

## Contributing
1. Fork the repository.
2. Create a feature branch (`git checkout -b feature/YourFeature`).
3. Commit changes (`git commit -m 'Add YourFeature'`).
4. Push to the branch (`git push origin feature/YourFeature`).
5. Open a pull request.

## License
This project is licensed under the MIT License.

## Resources
- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [MongoDB Laravel Package](https://github.com/mongodb/laravel-mongodb)
- [MongoDB PHP Driver](https://www.php.net/manual/en/mongodb.installation.php)
- [jQuery](https://jquery.com/)
- [Laravel Eloquent Relationships](https://laravel.com/docs/12.x/eloquent-relationships#many-to-many)