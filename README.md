# smf-young-tech-challenge

Laravel API for uploading invoice-like documents, extracting text, transforming it into structured data with Ollama, and saving the result into SQLite.

## Features

- upload document files (`pdf`, `jpg`, `jpeg`, `png`)
- store document metadata in SQLite
- list, show, and delete uploaded documents
- process documents through:
    - image OCR with Tesseract
    - PDF text extraction with `pdftotext`
    - structured invoice extraction with Ollama
- store extracted text in `ocr_text`
- store raw AI output in `ai_raw_response`
- persist parsed data into linked SQL tables:
    - `contractors`
    - `invoices`
    - `items`
    - `payments`
- read persisted invoices through API endpoints
- Swagger UI available for API documentation

## Architecture overview

The application is split into a few simple layers:

- `DocumentController` handles document upload, listing, details, deletion, metadata update, and processing
- `TextExtractionService` extracts text from uploaded files
    - `ImageTextExtractor` uses Tesseract OCR
    - `PdfTextExtractor` uses `pdftotext`
- `DocumentAiExtractionService` sends extracted text to Ollama and returns structured invoice data
- `InvoicePersistenceService` saves parsed data into SQLite tables: `contractors`, `invoices`, `items`, and `payments`
- `InvoiceController` exposes persisted invoice data through read endpoints
- OpenAPI / Swagger documents the public API

## Tech stack

- PHP 8.4
- Laravel 13
- SQLite
- Ollama

## Requirements

- PHP
- Composer
- Tesseract OCR
- `pdftotext`
- Ollama

## Configuration

Fill the following variables in your `.env` file:

```env
PDFTOTEXT_PATH=
OLLAMA_URL=http://127.0.0.1:11434
OLLAMA_MODEL=llama3.1
```

Example:

```env
PDFTOTEXT_PATH=C:\Tools\xpdf\pdftotext.exe
OLLAMA_URL=http://127.0.0.1:11434
OLLAMA_MODEL=llama3.1
```

`Tesseract` is expected to be available in the system PATH.

## Local setup

```bash
composer run setup
php artisan serve
```

Application URL:

```text
http://127.0.0.1:8000
```

## AI setup

Make sure Ollama is running locally and the configured model is available.

Example:

```bash
ollama pull llama3.1
ollama run llama3.1
```

## Optional: Laravel Herd

If you use Laravel Herd, you can serve the project through a local `.test` domain.

Example workflow:

```bash
herd link smf-young-tech-challenge
```

Then open:

```text
http://smf-young-tech-challenge.test
```

## Available endpoints

### Documents

- `POST /api/documents`
- `GET /api/documents`
- `GET /api/documents/{id}`
- `DELETE /api/documents/{id}`
- `POST /api/documents/{id}/process`

### Invoices

- `GET /api/invoices`
- `GET /api/invoices/{id}`

## Upload request example

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

## Swagger

Swagger UI is available at:

```text
http://127.0.0.1:8000/api/documentation
```
