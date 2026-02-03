# Multi-Language Support (i18n)

## Overview
The Sneeze-o-Meter application now supports both English and Dutch languages. Users can switch between languages using the language switcher in the navigation bar.

## Features
- **Language Switcher**: Located in the top navigation bar with EN/NL buttons
- **Session-based**: Language preference is stored in the user's session
- **Fallback**: Defaults to English if no language is selected
- **Full Coverage**: All user-facing text is translated including:
  - Navigation menu
  - Dashboard
  - Home page
  - Leaderboard
  - Admin panel
  - Forms and modals
  - Success/error messages
  - DataTables interface
  - Chart labels (days of week)

## Available Languages
1. **English (en)** - Default
2. **Dutch (nl)** - Nederlands

## Language Files
Translation strings are stored in:
- `lang/en/messages.php` - English translations
- `lang/nl/messages.php` - Dutch translations

## How to Add a New Language
1. Create a new directory in `lang/` with the language code (e.g., `lang/fr` for French)
2. Copy `lang/en/messages.php` to the new directory
3. Translate all strings in the copied file
4. Update the language switcher in `resources/views/layouts/navigation.blade.php`:
   ```blade
   <a href="{{ route('language.switch', 'fr') }}" 
      class="btn btn-sm {{ app()->getLocale() == 'fr' ? 'btn-primary' : 'btn-outline-secondary' }}" 
      title="Français">FR</a>
   ```
5. Update the route whitelist in `routes/web.php`:
   ```php
   if (in_array($locale, ['en', 'nl', 'fr'])) {
   ```
6. Update the AppServiceProvider whitelist in `app/Providers/AppServiceProvider.php`:
   ```php
   if (in_array($locale, ['en', 'nl', 'fr'])) {
   ```

## How to Change Default Language
Set the `APP_LOCALE` environment variable in `.env`:
```env
APP_LOCALE=nl  # For Dutch
# or
APP_LOCALE=en  # For English
```

Or update `config/app.php`:
```php
'locale' => env('APP_LOCALE', 'nl'),
```

## Usage in Blade Templates
Use the `__()` helper function or `@lang` directive:

```blade
<!-- Using __() helper -->
<h1>{{ __('messages.home.title') }}</h1>

<!-- With parameters -->
<p>{{ __('messages.admin.user_deleted', ['name' => $user->name]) }}</p>

<!-- Using @lang directive -->
<p>@lang('messages.home.welcome')</p>
```

## Usage in Controllers
```php
return redirect()->route('dashboard')
    ->with('success', __('messages.dashboard.sneeze_recorded'));
```

## Translation String Structure
The `messages.php` file is organized into logical sections:

```php
return [
    'nav' => [
        'home' => 'Home',
        'dashboard' => 'Dashboard',
        // ...
    ],
    'dashboard' => [
        'title' => 'Dashboard',
        'total_sneezes' => 'Total Sneezes',
        // ...
    ],
    // ...
];
```

Access nested translations using dot notation:
```blade
{{ __('messages.nav.home') }}
{{ __('messages.dashboard.title') }}
```

## JavaScript Translations
For JavaScript strings, use Blade to render the translation:
```javascript
locationStatus.innerHTML = '{{ __('messages.record.detecting_location') }}';
```

## Testing
1. Open the application in a browser
2. Click the EN/NL buttons in the navigation bar
3. Verify all text updates to the selected language
4. Test forms, modals, and DataTables to ensure all elements are translated

## Notes
- Language preference persists across page loads (stored in session)
- Refreshing the page maintains the selected language
- New users will see the default language (English)
- The language switcher is visible to both authenticated and guest users
