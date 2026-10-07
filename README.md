# PHP SMM Panel Website

This project is a lightweight PHP-based SMM panel landing page and order form that can connect to a real SMM provider API.

## Features
- Responsive marketing landing page
- Services section
- Order form with API-ready payload
- Dashboard-style metrics panel
- Configurable API integration using environment variables
- Fallback demo data when the provider is unavailable

## Requirements
- PHP 8.1+
- Composer

## Setup
1. Copy `.env.example` to `.env`
2. Update the API credentials:
   - `SMM_API_BASE_URL`
   - `SMM_API_KEY`
   - `SMM_API_USERNAME`
3. Install dependencies:
   ```bash
   composer install
   ```
4. Start the PHP built-in server:
   ```bash
   php -S localhost:8000 -t public
   ```
5. Open `http://localhost:8000`

## API Expectations
This website calls these endpoints:
- `GET {SMM_API_BASE_URL}/api/services`
- `POST {SMM_API_BASE_URL}/api/orders`

Each request sends the API key and username alongside the order data.

## Notes
If you do not provide a working API URL, the page automatically falls back to sample service data so the site remains usable.
