# Product Requirements Document (PRD)

## Visiting Card Management System (VCMS)

**Version:** 1.0  
**Date:** April 2026  
**Status:** Draft

---

## 1. Overview

### 1.1 Product Summary

The Visiting Card Management System (VCMS) is a web-based application that digitizes, organizes, and leverages visiting card data collected at events, meetings, and networking opportunities. The system allows users to store rich contact information, upload card images, manage email configurations, and send personalized emails directly to contacts — all from a single interface.

### 1.2 Problem Statement

Professionals collect dozens of visiting cards at events but have no structured system to:

- Store and search card data quickly
- Remember where and when they met someone
- Follow up via email using personalized templates
- Attach the original card image as proof or reference

### 1.3 Goals

- Digitize and centralize visiting card data
- Enable fast retrieval and filtering of contacts
- Allow personalized email follow-ups with template support
- Support image attachments (card photos) in outgoing emails

---

## 2. User Roles

| Role  | Description                                            |
| ----- | ------------------------------------------------------ |
| Admin | Full access: manage contacts, templates, SMTP settings |
| User  | Can add/view contacts and send emails                  |

---

## 3. Core Modules

### 3.1 Contact Management (Visiting Card Data)

#### 3.1.1 Contact Fields

| Field            | Type            | Notes                                                 |
| ---------------- | --------------- | ----------------------------------------------------- |
| Country          | Dropdown        | Country name from standard list                       |
| Name             | Text            | Full name of the contact                              |
| Company Name     | Text            | Organization/company                                  |
| Phone Numbers    | Repeatable      | Multiple entries allowed; label (mobile/office/other) |
| Email Addresses  | Repeatable      | Multiple entries allowed; label (work/personal/other) |
| Event            | Text / Dropdown | Where the card was collected (e.g., "TechConf 2026")  |
| Card Front Image | Image Upload    | JPG/PNG, max 5MB                                      |
| Card Back Image  | Image Upload    | JPG/PNG, max 5MB                                      |
| Other Image      | Image Upload    | Any relevant image, max 5MB                           |
| Notes            | Textarea        | Optional internal notes                               |
| Date Added       | Auto            | Timestamp on creation                                 |

#### 3.1.2 Contact List View

- Paginated table with search and filters
- Filter by: Country, Company, Event, Date Range
- Columns: Name, Company, Country, Event, Date Added, Actions
- Actions: View, Edit, Delete, Send Email

#### 3.1.3 Contact Detail View

- Full card display with all fields
- Image viewer (Front / Back / Other) with lightbox
- Email history tab showing all emails sent to this contact
- Quick action: "Send Email"

---

### 3.2 Email Configuration (SMTP Settings)

Admins can configure one or more email sender accounts.

#### 3.2.1 SMTP Settings Fields

| Field              | Description                       |
| ------------------ | --------------------------------- |
| Configuration Name | Label (e.g., "Company Gmail")     |
| From Name          | Display name in outgoing email    |
| From Email         | Sender email address              |
| SMTP Host          | e.g., smtp.gmail.com              |
| SMTP Port          | e.g., 587, 465                    |
| Encryption         | TLS / SSL / None                  |
| SMTP Username      | Auth username                     |
| SMTP Password      | Auth password (encrypted at rest) |
| Status             | Active / Inactive                 |

#### 3.2.2 Features

- Multiple SMTP configurations supported
- Test connection button (sends test email)
- Only Active configurations appear in the Send Email flow

---

### 3.3 Email Template Management

Users can create and manage reusable email templates.

#### 3.3.1 Template Fields

| Field         | Description                                  |
| ------------- | -------------------------------------------- |
| Template Name | Internal label                               |
| Subject Line  | Email subject (supports variables)           |
| Body          | Rich text / HTML editor (supports variables) |
| Status        | Active / Draft                               |

#### 3.3.2 Template Variables

Variables are auto-replaced with contact data at send time:

| Variable          | Replaced With            |
| ----------------- | ------------------------ |
| `{{name}}`        | Contact's full name      |
| `{{company}}`     | Contact's company name   |
| `{{event}}`       | Event where they met     |
| `{{country}}`     | Contact's country        |
| `{{sender_name}}` | From Name in SMTP config |

#### 3.3.3 Features

- Rich text editor (TipTap / CKEditor / Quill)
- Variable insertion helper (click to insert)
- Preview mode (renders with sample data)
- Duplicate template
- Active/Draft toggle

---

### 3.4 Send Email Flow

Triggered from the Contact Detail view or Contact List (bulk).

#### 3.4.1 Step-by-Step Flow

**Step 1 — Select Recipient Email**

- Display all email addresses stored for the contact
- Allow selection of one or multiple recipient emails
- Each email shown with its label (work / personal / other)

**Step 2 — Select Email Configuration**

- Dropdown of active SMTP configurations
- Shows "From Name <from_email>" for each option

**Step 3 — Select Email Template**

- Dropdown of active templates
- Live preview panel renders selected template with contact variables replaced

**Step 4 — Attachments**

- Toggle options to attach:
    - ✅ Card Front Image
    - ✅ Card Back Image
    - ✅ Other Image
- Each toggle shows a thumbnail preview
- Option to upload an additional custom attachment

**Step 5 — Review & Send**

- Summary panel: To, From, Subject, Template Preview
- "Send Now" button
- Optional: "Schedule for Later" (Phase 2)

#### 3.4.2 Post-Send

- Success/failure toast notification
- Email log entry created with: timestamp, template used, config used, attachments, status (sent/failed)
- Viewable in Contact Detail → Email History

---

## 4. Email History Log

For each contact, maintain a log of all sent emails.

| Field       | Description              |
| ----------- | ------------------------ |
| Sent At     | Timestamp                |
| To          | Recipient email(s)       |
| From        | Sender config used       |
| Subject     | Rendered subject         |
| Template    | Template name used       |
| Attachments | List of attached files   |
| Status      | Sent / Failed            |
| Error       | If failed, error message |

---

## 5. Technical Requirements

### 5.1 Stack Recommendation

| Layer        | Technology                                               |
| ------------ | -------------------------------------------------------- |
| Backend      | Laravel (PHP)                                            |
| Frontend     | Laravel Blade + Alpine.js / Livewire or Vue.js           |
| Database     | MySQL / PostgreSQL                                       |
| File Storage | Local disk or S3-compatible (configurable)               |
| Email        | Laravel Mail with dynamic SMTP (config per send)         |
| Queue        | Laravel Queues (Redis / database driver) for async email |

### 5.2 Image Handling

- Images stored in `storage/app/cards/{contact_id}/`
- Thumbnail generation on upload (max 300x300 for list views)
- Original preserved for attachments
- Accepted formats: JPG, JPEG, PNG, WEBP
- Max file size: 5MB per image

### 5.3 Security

- SMTP passwords encrypted using Laravel's `encrypt()` helper
- Role-based access control (Admin / User)
- CSRF protection on all forms
- Input sanitization on all text fields
- File type validation (MIME check, not just extension)

### 5.4 Performance

- Contacts list paginated (25 per page default)
- Images served via CDN or optimized storage URLs
- Emails dispatched via queue (non-blocking UI)

---

## 6. Database Schema (Simplified)

```
contacts
  id, country, name, company_name, event, notes, created_at, updated_at

contact_phones
  id, contact_id, phone, label

contact_emails
  id, contact_id, email, label

contact_images
  id, contact_id, type (front|back|other), file_path, file_name

email_configurations
  id, name, from_name, from_email, host, port, encryption, username, password (encrypted), status

email_templates
  id, name, subject, body, status

email_logs
  id, contact_id, config_id, template_id, recipients (JSON), subject, attachments (JSON), status, error, sent_at
```

---

## 7. UI/UX Requirements

- Mobile-responsive layout
- Clean sidebar navigation: Contacts | Templates | Email Config | Logs
- Dashboard showing: Total Contacts, Emails Sent (30d), Top Events
- Consistent card-style detail view
- Drag-and-drop image upload zones
- Inline validation on all forms

---

## 8. Out of Scope (Phase 1)

- Bulk import via CSV/Excel (Phase 2)
- OCR / AI card scanning (Phase 2)
- Scheduled / drip email sequences (Phase 2)
- Mobile app (Phase 3)
- CRM integration (Salesforce, HubSpot) (Phase 3)

---

## 9. Milestones

| Milestone | Scope                                           | Estimated Duration |
| --------- | ----------------------------------------------- | ------------------ |
| M1        | DB schema, auth, contact CRUD, image upload     | 2 weeks            |
| M2        | Email config, template builder, variable system | 1.5 weeks          |
| M3        | Send Email flow, email logs, queue setup        | 1.5 weeks          |
| M4        | Dashboard, polish, testing, deployment          | 1 week             |

**Total Estimated: ~6 weeks**

---

## 10. Acceptance Criteria

- [ ] Admin can add/edit/delete contacts with all fields and images
- [ ] Multiple phones and emails per contact can be added/removed
- [ ] Admin can configure and test SMTP settings
- [ ] Admin can create templates with variables and preview them
- [ ] User can select recipient emails from contact's stored emails
- [ ] User can select SMTP config and template before sending
- [ ] User can toggle card image attachments before sending
- [ ] Email is dispatched via queue; success/failure is logged
- [ ] Email history is viewable per contact
- [ ] All images are viewable in a lightbox on contact detail page

---

_Document prepared for development handoff. Subject to revision based on stakeholder feedback._
