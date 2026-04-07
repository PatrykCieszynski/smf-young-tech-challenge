# smf-young-tech-challenge

Simple Laravel API for uploading and processing invoice-like documents.

## Current scope

This project currently provides a basic `documents` flow:

- upload a document file (`pdf`, `jpg`, `jpeg`, `png`)
- store document metadata in SQLite
- list uploaded documents
- get document details
- delete a document
- process uploaded documents
- extract text from images using Tesseract OCR
- extract text from PDF files using `pdftotext`
- store extracted text in the `ocr_text` field

At this stage, the next step is AI-based structured data extraction and saving parsed invoice data into SQL models.

## Tech stack

- PHP 8.4
- Laravel 13
- SQLite

## Requirements

- PHP
- Composer
- Tesseract OCR
- `pdftotext`

## Configuration

Add the following variable to your `.env` file:

```env
PDFTOTEXT_PATH=
```

Example:

```env
PDFTOTEXT_PATH=C:\Tools\xpdf\pdftotext.exe
```

`Tesseract` is expected to be available in the system PATH.


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
- document processing endpoint
- image OCR with Tesseract
- PDF text extraction with `pdftotext`
- extracted text storage in `ocr_text`

### Planned next

- AI extraction to structured invoice data
- invoice-related SQL models
- Swagger documentation
