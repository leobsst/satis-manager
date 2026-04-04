# Satis Manager

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<p align="center">
  <a href="https://github.com/leobsst/satis-manager"><img src="https://img.shields.io/badge/version-1.2.0-blue.svg" alt="Version"></a>
  <a href="https://github.com/leobsst/satis-manager/actions?query=workflow%3Arun-tests+branch%3A1.x"><img src="https://img.shields.io/github/actions/workflow/status/leobsst/satis-manager/run-tests.yml?branch=1.x&label=tests&style=flat-square" alt="GitHub Tests Action Status"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-green.svg" alt="License"></a>
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?logo=laravel" alt="Laravel"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php" alt="PHP"></a>
</p>

## About Satis Manager

**Satis Manager** is a modern, Laravel-based web application that provides a comprehensive management interface for creating and maintaining private Composer package repositories. Built on top of [Composer Satis](https://github.com/composer/satis), it offers a user-friendly admin panel powered by [Filament](https://filamentphp.com) to manage multiple private PHP packages from various Git hosting providers.

Perfect for teams and organizations that need to:
- Host private PHP packages internally
- Manage multiple Git repositories as Composer packages
- Automate package builds via webhooks
- Control access with role-based permissions
- Support multiple Git providers (GitHub, GitLab, Bitbucket, self-hosted)

## Features

### Repository Management
- Add and manage Git repositories from multiple providers
- Support for **GitHub**, **GitLab**, **Bitbucket**, and **custom Git servers**
- Automatic URL construction based on provider configuration
- Intuitive Filament admin interface

### Per-Repository Credentials
- Store named credentials (token, username, domain) **per repository**, encrypted at rest
- Each repository can use its own credential or fall back to the global env-based config
- Credentials can be created and edited **inline** from the repository form
- Support for GitHub tokens, GitLab tokens, Bitbucket OAuth consumer keys, and custom HTTP basic auth

### Excluded Branch Patterns
- Define **glob patterns** per repository (e.g. `dependabot/*`, `renovate/**`) to exclude branches from the satis build
- Matched `dev-*` versions are automatically removed from the generated package metadata after each build
- Prevents Dependabot and Renovate branches from polluting your package list

### Package Building & Automation
- **Webhook integration** to trigger automatic builds on repository updates
- Background job processing for non-blocking package builds
- Dynamic `satis.json` generation from database repositories
- Automatic versioning support for all repository branches and tags

### Authentication & Security
- **Multi-factor authentication** (App-based and Email-based)
- **Role-based access control** with Spatie Permissions
- **OAuth2/Passport API** authentication for third-party integrations
- **Basic authentication** for Composer package downloads
- Email verification for user accounts
- Credentials encrypted at rest via `APP_KEY` (AES-256-CBC)

### Composer Repository
- Generates Composer-compatible package metadata
- Serves `packages.json` and vendor-specific package files
- Supports authentication for private Git repositories
- RESTful JSON endpoints for package definitions

### Admin Panel Features
- User management with role assignment
- Credential management with encrypted storage
- OAuth client management for API access
- Job monitoring and failed job tracking
- Repository configuration interface
- Log management with Filament Log Manager

## Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MariaDB / MySQL
- Git

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/leobsst/satis-manager.git
cd satis-manager
```

### 2. Quick Setup

Run the automated setup script:

```bash
composer setup
```

This command will:
- Install Composer dependencies
- Generate `.env` file from `.env.example`
- Generate Laravel application key
- Generate Passport encryption keys
- Install NPM dependencies
- Build frontend assets

### 3. Configure Environment

Edit the `.env` file with your configuration:

```env
# Application
APP_NAME="Satis Manager"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_VENDOR=vendor

# DEFAULT ADMIN ACCOUNT
DEFAULT_ADMIN_EMAIL=

# Satis configuration
SATIS_ARCHIVE=true

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=satis_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Database Setup

Run migrations and seed the database:

```bash
php artisan migrate --seed
```

### 5. Build assets

Build and cache required assets.

```bash
php artisan deploy
```

### 6. Start the Queue Worker

The application uses queues for background package building:

```bash
php artisan queue:work
```

For production, configure a process manager like Supervisor to keep the queue worker running.

### 7. API Credentials

Generate API credentials for authentication when calling webhooks

```bash
php artisan passport:client --client
```

Keep generated credentials.

#### Authentication method

- Grant type: `client_credentials`
- client_id: `Clien ID` **generated before**
- client_secret: `Client Secret` **generated before**

## Usage

### Accessing the Admin Panel

Navigate to `https://your-domain.com/admin/login` and log in with your admin credentials.

- email: `default_admin_email` or `admin@admin.net`
- password: `password`

### Adding a Repository

1. Go to **Repositories** in the admin panel
2. Click **New Repository**
3. Enter the vendor name (e.g., `yourcompany`)
4. Enter the repository name (e.g., `my-package`)
5. Select the Git provider (GitHub, GitLab, Bitbucket, or Custom)
6. Optionally assign a **credential** — select an existing one or create a new one inline
7. Optionally add **excluded branch patterns** (e.g. `dependabot/*`) to keep your package list clean
8. Click **Create**

The system will automatically construct the full Git URL based on the provider.

### Managing Credentials

Credentials allow you to configure per-repository authentication instead of relying solely on global environment variables.

1. Go to **Credentials** in the admin panel
2. Click **New Credential**
3. Give it a recognisable name (e.g. `My Org GitHub Token`)
4. Select the provider and fill in the required fields:
   - **GitHub / GitLab**: token only
   - **Bitbucket**: consumer key + consumer secret
   - **Custom**: token, optional username, and domain
5. Save — the token is stored encrypted and never displayed in logs

> Credentials set on a repository override the global env variables for the same provider during the build. If no credential is assigned, the global config is used as fallback.

### Setting Up Webhooks

To enable automatic package building when you push to your Git repository:

#### 1. Generate an API Token

First, you need to generate a Bearer token for webhook authentication:

1. Go to **OAuth Clients** in the admin panel
2. Click **New OAuth Client**
3. Enter a name for your client (e.g., "Webhook Client")
4. Select the appropriate grant type (Personal Access Client recommended)
5. Click **Create**
6. Copy the generated **Client ID** and **Client Secret**

To obtain a Bearer token, make a POST request to your Passport token endpoint:

```bash
curl -X POST https://your-domain.com/oauth/token \
  -H "Content-Type: application/json" \
  -d '{
    "grant_type": "client_credentials",
    "client_id": "your-client-id",
    "client_secret": "your-client-secret",
    "scope": "*"
  }'
```

This will return a response containing your access token:

```json
{
  "token_type": "Bearer",
  "expires_in": 31536000,
  "access_token": "your-access-token-here"
}
```

#### 2. Configure the Webhook

Now configure the webhook in your Git repository:

1. Go to your Git repository settings (GitHub, GitLab, Bitbucket, etc.)
2. Navigate to the Webhooks section
3. Add a new webhook with the following settings:
   - **URL**: `https://your-domain.com/webhooks/packages/build`
   - **Content type**: `application/json`
   - **Authentication**: Add a custom header `Authorization: Bearer your-access-token-here`
   - **Events**: Select "Push" and "Release" events
   - **Payload**: The webhook expects the following JSON structure:
     ```json
     {
       "provider": "github",
       "source_repo": "vendor/repository-name",
       "tag": "v1.0.0"
     }
     ```
4. Save the webhook

#### 3. Testing the Webhook

You can test your webhook configuration with curl:

```bash
curl -X POST https://your-domain.com/webhooks/packages/build \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your-access-token-here" \
  -d '{
    "provider": "github",
    "source_repo": "yourcompany/your-package",
    "tag": "v1.0.0"
  }'
```

A successful response will return:

```json
{
  "status": "success"
}
```

#### Webhook Payload Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `provider` | string | Yes | Git provider: `github`, `gitlab`, `bitbucket`, or `custom` |
| `source_repo` | string | Yes | Repository identifier in format `vendor/repository-name` |
| `tag` | string | No | Specific tag or version to build (e.g., `v1.0.0`) |

When you push changes or create a release, the webhook will trigger a background job to rebuild your packages automatically.

### Using the Repository in Your Projects

Add the repository to your project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://your-domain.com"
        }
    ]
}
```

```bash
composer config http-basic.your-domain.com your-username your-password
```

Your username is your email.

#### Important: Archive Configuration

The `SATIS_ARCHIVE` environment variable controls how packages are distributed:

- **`SATIS_ARCHIVE=true` (Recommended)**: Packages are archived as `.tar` files and served directly from your Satis server. Clients use HTTP basic authentication only.

- **`SATIS_ARCHIVE=false`**: Packages are installed via `git clone` (source). The system automatically removes GitHub/GitLab download URLs from the package metadata. Clients must configure Git credentials:
  ```bash
  # For GitHub repositories
  composer config github-oauth.github.com your-github-token

  # For GitLab repositories
  composer config gitlab-oauth.gitlab.com your-gitlab-token
  ```

**Recommendation**: Keep `SATIS_ARCHIVE=true` in your `.env` file for the best user experience with simple HTTP basic authentication.

> **How to verify it's working**: When `SATIS_ARCHIVE=false`, check your logs after a build:
> ```bash
> tail storage/logs/satis-*.log | grep "Post-processing"
> # Should show: "Post-processing complete - removed X dist URLs to force source installation"
> ```

Then install your private packages as usual:

```bash
composer require yourcompany/my-package
```

### Managing Users

1. Go to **Users** in the admin panel
2. Create new users and assign them roles
3. Users must verify their email before accessing the panel
4. Admin role is required to access the Filament panel

### API Access

The application supports OAuth2 authentication via Laravel Passport:

1. Go to **OAuth Clients** in the admin panel
2. Create a new client for your application
3. Use the client credentials to obtain access tokens
4. Use tokens to authenticate API requests

## Development

### Running Locally

```bash
# Start the development server
php artisan serve

# Start Vite for frontend hot reload
npm run dev

# Run the queue worker
php artisan queue:work
```

### Code Quality Tools

```bash
# Run tests
php artisan test

# Static analysis
./vendor/bin/phpstan analyse

# Code formatting
./vendor/bin/pint
```

## Project Structure

```
app/
├── Models/              # Eloquent models (Repository, User, etc.)
├── Http/
│   ├── Controllers/     # HTTP controllers
│   └── Middleware/      # Custom middleware
├── Filament/            # Filament admin panel resources
├── Services/            # Business logic services
├── Jobs/                # Background job classes
├── Enums/               # PHP enums (CodespaceProviderEnum)
└── Providers/           # Service providers

routes/
├── web.php              # Web routes
└── webhooks.php         # Webhook endpoints

database/
├── migrations/          # Database migrations
└── seeders/             # Database seeders

resources/
├── views/               # Blade templates
└── css/                 # Frontend styles

public/                  # Public web directory
storage/
└── app/satis/           # Generated Satis package metadata
```

## Configuration

### Authentication

All authentication is managed through the **Credentials** section of the admin panel. There are no global environment variables for Git provider tokens — each credential is stored encrypted in the database and assigned per repository.

Create a named credential for each access token or deploy key you use, then assign it to the relevant repositories. The same credential can be reused across multiple repositories.

| Provider | Required fields | Optional |
|----------|----------------|----------|
| GitHub | Token | Username (for HTTP basic instead of oauth) |
| GitLab | Token | Username (required for deploy tokens) |
| Bitbucket | Consumer Key + Consumer Secret | — |
| Custom | Token + Domain | Username |

## Security

### Reporting Vulnerabilities

If you discover a security vulnerability, please email the maintainer directly. All security vulnerabilities will be promptly addressed.

### Best Practices

- Always use HTTPS in production
- Keep Git provider tokens secure
- Regularly update dependencies
- Enable email verification for users
- Use strong passwords and MFA
- Configure proper file permissions on storage directories

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Credits

- Built with [Laravel](https://laravel.com)
- Admin panel by [Filament](https://filamentphp.com)
- Repository builder by [Composer Satis](https://github.com/composer/satis)
- Developed by [LEOBSST](https://github.com/leobsst) / [B.L.A.M. PRODUCTION](https://linksly.fr/BLAM-PRODUCTION)

## Support

For issues, questions, or feature requests, please open an issue on GitHub.

---

<p align="center">Made with ❤️ using Laravel & Filament</p>
