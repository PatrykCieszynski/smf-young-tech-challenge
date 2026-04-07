# smf-young-tech-challenge

Simple Laravel API for uploading and processing invoice-like documents.

## Current scope

This project currently provides a basic `documents` flow:

- upload a document file (`pdf`, `jpg`, `jpeg`, `png`)
- store document metadata in SQLite
- list uploaded documents
- get document details
- delete a document
- trigger a placeholder processing action

At this stage, the processing endpoint is a foundation for the next steps:

- text extraction (OCR / PDF text extraction)
- AI-based structured data extraction
- saving parsed invoice data into SQL models

## Tech stack

- PHP 8.4
- Laravel 13
- SQLite

## Requirements

- PHP
- Composer
- Node.js / npm

## Local setup

```bash
composer run setup
```

```bash
php artisan serve
```

## Optional: Laravel Herd

If you use Laravel Herd, you can serve the project through a local .test domain.

Example workflow:
```bash
herd link smf-young-tech-challenge
```

## Available endpoints

### Documents

- `POST /api/documents`
- `GET /api/documents`
- `GET /api/documents/{id}`
- `DELETE /api/documents/{id}`
- `POST /api/documents/{id}/process`

## Request example

### Upload document

**Endpoint:**

```text
POST /api/documents
```

**Content type:**

```text
multipart/form-data
```

**Form field:**

- `file` - uploaded file

**Supported file types:**

- `pdf`
- `jpg`
- `jpeg`
- `png`

## Current status

### Implemented

- document database model
- document migration
- file upload endpoint
- list, details and delete endpoints
- placeholder processing endpoint

### Planned next

- PDF text extraction
- OCR for images
- AI extraction to structured invoice data
- invoice-related SQL models
- Swagger documentation
