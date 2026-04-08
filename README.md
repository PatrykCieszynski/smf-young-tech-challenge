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
- extract structured invoice data with Ollama
- store extracted text in the `ocr_text` field
- store raw AI output in the `ai_raw_response` field
- persist parsed invoice data into SQL tables
- create linked records in `contractors`, `invoices`, `items`, and `payments`

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

## AI setup

Make sure Ollama is running locally and the configured model is available.

Example:

```cmd
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
- AI-based structured invoice data extraction with Ollama
- extracted text storage in `ocr_text`
- raw AI response storage in `ai_raw_response`
- parsed invoice data persistence to SQL tables
- linked invoice storage in `contractors`, `invoices`, `items`, and `payments`

### Planned next

- Swagger documentation
