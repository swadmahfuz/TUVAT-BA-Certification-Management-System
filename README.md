<div id="top"></div>

# 🛡️ TÜV Austria BIC BA Certification Management System

## Business Assurance / ISO Certificate Verification System

This application is a dedicated **BA Certification CVS** module for managing, tracking, importing, exporting, and verifying **Business Assurance / ISO certification records** issued by TÜV Austria BIC.

It is designed as a separate Laravel application for the BA Certification function, while sharing the same common database and user session structure used by other TÜV Austria CVS modules.

---

## 📌 Overview

The **BA Certification Management System** helps TÜV Austria BIC manage ISO / Business Assurance certification records in a structured and traceable way.

The system supports client management, certification standard management, accreditation body management, certificate lifecycle tracking, PDF upload, audit report upload, Excel import/export, review/approval workflow, and public certificate verification.

The public verification page allows external users to verify a BA / ISO certificate using the certificate number.

For global ISO certification database verification, users may also visit:

```text
https://www.iafcertsearch.org/
```

---

## 🗂️ Key Features

### Client Management

* Add new BA clients
* Edit client information
* View client details
* Link multiple certificates to one client

### Certificate Management

* Add BA / ISO certificate records
* Edit certificate records
* View full certificate details
* Track certificate number
* Track certificate scope
* Track certification standard
* Track accreditation body
* Track certificate issue and expiry date
* Track certificate status
* Track audit status

### Dynamic Master Data

* Manage certification standards
* Manage accreditation bodies
* Add new standards dynamically
* Add new accreditation bodies dynamically

### Audit Cycle Tracking

The system tracks important certification cycle dates:

* Initial certification audit completion date
* Surveillance 1 due date
* Surveillance 2 due date
* Recertification due date
* Six-month grace period end date

### Audit Team Tracking

The system can record:

* Lead auditor
* Auditor 1
* Auditor 2
* Auditor 3
* Technical expert

### Review and Approval Workflow

Each certificate record follows an internal workflow:

```text
Pending Review → Pending Approval → Approved
```

The system records:

* Created by
* Reviewed by
* Approved by
* Updated by
* Deleted by

### PDF and Audit Report Upload

The system supports:

* Certificate PDF upload
* Certificate PDF view/download
* Multiple audit report uploads
* Audit report upload by audit year
* Audit report upload by audit type
* Audit report view/download

### Public Certificate Verification

Public users can verify BA certificates by certificate number.

The verification page displays:

* Client name
* Client address
* Certification standard
* Accreditation body
* Certificate number
* Certificate scope
* Issue date
* Expiry date
* Surveillance due dates
* Recertification due date
* Certificate status
* Certificate PDF, if uploaded

### Excel Import and Export

The system supports:

* Excel export of BA certificate database
* Excel import of BA certificate records
* Automatic client creation during import
* Automatic standard creation during import
* Automatic accreditation body creation during import
* Automatic surveillance and recertification date calculation during import

---

## 🧩 BA Module Tables

The BA Certification CVS module uses the following main tables:

```text
ba_clients
ba_standards
ba_accreditation_bodies
ba_certificates
ba_audit_reports
```

It also uses the shared common table:

```text
users
```

---

## 🔐 Shared Login and Database

This BA CVS module is intended to operate as part of the wider TÜV Austria CVS ecosystem.

Although this is a separate Laravel application, it may share:

```text
1. Common database
2. Common users table
3. Common session configuration
4. Common login across subdomains
```

Recommended `.env` session configuration:

```env
SESSION_DOMAIN=.yourdomain.com
SESSION_DRIVER=database
```

Example:

```env
SESSION_DOMAIN=.tuvat-cvs.com
SESSION_DRIVER=database
```

This allows login/session sharing across related CVS subdomains.

---

## 🧱 Built With

* Laravel 8.83.25
* PHP 7.4.30
* MySQL / MariaDB
* Bootstrap
* Font Awesome
* Maatwebsite Excel
* QR Code API
* HTML
* CSS
* JavaScript / jQuery

---

## 🚀 Installation

### 1. Clone or copy the BA CVS project

Place the BA CVS application in its own project directory.

Example:

```text
verify-cert-certification
```

Suggested subdomain:

```text
certification.yourdomain.com
```

---

### 2. Configure `.env`

Set the correct app URL:

```env
APP_NAME="TUV Austria BA Certification CVS"
APP_URL=https://certification.yourdomain.com
```

Set the common database:

```env
DB_DATABASE=your_common_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

Set shared session configuration:

```env
SESSION_DOMAIN=.yourdomain.com
SESSION_DRIVER=database
```

---

### 3. Run migrations

Run only the safe migration command:

```sh
php artisan migrate
```

Do not run destructive migration commands on a shared database.

---

### 4. Clear cache

After configuration or route changes, run:

```sh
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

### 5. Login

Login using a user from the shared `users` table.

Registration may remain disabled if user creation is centrally controlled.

---

## ⚠️ Migration Warning

Because this BA CVS app may share the same database with other CVS modules, do not run the following commands on production or shared database:

```sh
php artisan migrate:fresh
php artisan migrate:refresh
php artisan migrate:reset
```

These commands may drop or reset tables used by other CVS modules.

Use only:

```sh
php artisan migrate
```

Always backup the database before migration.

---

## 📊 Import Template Columns

The BA import file should use the following column headers:

```text
client_name
client_address
contact_person
email
phone
client_remarks
standard_name
standard_code
accreditation_body
accreditation_body_short_name
certificate_number
certificate_scope
certification_cycle
certificate_issue_date
certificate_expiry_date
initial_certification_audit_completion_date
audit_status
certificate_status
lead_auditor
auditor_1
auditor_2
auditor_3
technical_expert
review_by
review_by_email
approval_by
approval_by_email
remarks
```

Recommended date format:

```text
YYYY-MM-DD
```

Example:

```text
2026-06-01
```

---

## ✅ Basic Usage

### Internal Users

Internal users can:

* Login to the BA dashboard
* Add clients
* Add certificates
* Upload certificate PDFs
* Upload audit reports
* Review certificates
* Approve certificates
* Track upcoming audits
* Track expired certificates
* Import Excel data
* Export Excel data

### Public Users

Public users can:

* Search certificate number
* Verify certificate authenticity
* View certificate details
* Download or view certificate PDF, if available

---

## 📁 Important Routes

Common BA app routes include:

```text
/dashboard
/clients
/add-client
/add-certificate
/pending-certificates
/upcoming-audits
/expired-certificates
/manage-standards
/manage-accreditation-bodies
/deleted-certificates
/imports-exports
```

Public verification route:

```text
/?search=CERTIFICATE-NUMBER
```

---

## 🧪 Testing Checklist

Before deployment, test:

```text
01. Login
02. Logout
03. Dashboard loading
04. Add client
05. Edit client
06. View client
07. Add certificate
08. Edit certificate
09. View certificate
10. Upload certificate PDF
11. Upload audit report
12. Review certificate
13. Approve certificate
14. Public certificate verification
15. Upcoming audits page
16. Expired certificates page
17. Deleted certificate restore
18. Manage standards
19. Manage accreditation bodies
20. Excel import
21. Excel export
```

---

## 🌐 IAF CertSearch Reference

This BA CVS verifies certificates issued and approved within the TÜV Austria BIC BA Certification Management System.

For global ISO certification database verification, users may also visit:

```text
https://www.iafcertsearch.org/
```

---

## 🔮 Future Enhancements

Possible future improvements include:

* Automated email reminders
* Certificate expiry alerts
* Surveillance audit reminder dashboard
* Client portal
* Central admin panel
* Global certification database integration
* API-based certificate verification
* Advanced reporting dashboard
* Standard-wise certificate reports
* Accreditation body-wise reports

---

## 📄 License

Distributed under the GPL-3.0 License. See `LICENSE` for more information.

---

## 👤 Contact

Swad Ahmed Mahfuz
Head of Division, Business Assurance & Training
TÜV Austria Bangladesh Office

Email:

```text
contact@swadmahfuz.com
swad.mahfuz@gmail.com
```

---

<p align="right">(<a href="#top">back to top</a>)</p>
