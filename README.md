# Student Registration System — EC2 + Nginx + PHP + RDS (MySQL)

A simple student registration web app deployed on AWS: a static HTML form served by
Nginx, processed by PHP-FPM, and stored in an Amazon RDS MySQL database.

![Registration Form](screenshots/registration-form.png)

## Architecture

```
Browser → EC2 (Nginx + PHP-FPM) → Amazon RDS (MySQL)
```

- **EC2** — Amazon Linux 2023, t2.micro, runs Nginx + PHP-FPM
- **Nginx** — serves `index.html`, proxies `.php` requests to PHP-FPM
- **PHP-FPM** — executes `insert.php`
- **Amazon RDS (MySQL)** — stores registration records in the `studentdb` database

## Repo contents

| File | Purpose |
|---|---|
| `index.html` | Registration form (frontend) |
| `insert.php` | Handles form submission, inserts into RDS via prepared statements |
| `config.sample.php` | Template for DB credentials — copy to `config.php` on the server |
| `schema.sql` | Creates the `studentdb` database and `students` table |
| `.gitignore` | Keeps real `config.php` (with real credentials) out of git |
| `screenshots/` | Screenshots of the working deployment |

> **Security note:** the original version of `insert.php` built SQL with string
> concatenation and hardcoded credentials directly in the file. This repo version
> uses **prepared statements** (prevents SQL injection) and loads credentials from
> a git-ignored `config.php` (prevents leaking secrets to GitHub). Copy
> `config.sample.php` → `config.php` on your server and fill in real values.

## Deployment steps

### 1. Launch EC2
- AMI: Amazon Linux 2023
- Instance type: t2.micro (Free Tier)
- Security group:
  - HTTP (80) → Anywhere (0.0.0.0/0)
  - SSH (22) → Your IP

```bash
ssh -i your-key.pem ec2-user@EC2_PUBLIC_IP
```

### 2. Install Nginx
```bash
sudo dnf update -y
sudo dnf install nginx -y
sudo systemctl enable nginx
sudo systemctl start nginx
```

### 3. Install PHP
```bash
sudo dnf install php php-fpm php-mysqlnd -y
sudo systemctl enable php-fpm
sudo systemctl start php-fpm
```

### 4. Configure Nginx for PHP
Edit `/etc/nginx/nginx.conf`, replace the `location /` block:

```nginx
location / {
    index index.html index.php;
}

location ~ \.php$ {
    root /usr/share/nginx/html;
    fastcgi_pass unix:/run/php-fpm/www.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

```bash
sudo nginx -t
sudo systemctl restart nginx
sudo systemctl restart php-fpm
```

### 5. Create the RDS MySQL database
- Engine: MySQL
- DB name: `studentdb`
- Username: `admin`
- Copy the RDS endpoint after creation, e.g.
  `studentdb.xxxxxxxxx.ap-south-1.rds.amazonaws.com`

### 6. RDS security group
Allow inbound MySQL/Aurora (port 3306) **from the EC2 security group**, not from
the whole internet.

### 7. Connect EC2 → RDS and create the schema
```bash
sudo dnf install mariadb105 -y
mysql -h RDS-ENDPOINT -u admin -p < schema.sql
```

### 8. Deploy the app files
```bash
sudo cp index.html insert.php /usr/share/nginx/html/
sudo cp config.sample.php /usr/share/nginx/html/config.php
sudo nano /usr/share/nginx/html/config.php   # fill in real RDS endpoint + password
sudo chmod 644 /usr/share/nginx/html/index.html /usr/share/nginx/html/insert.php
sudo chmod 640 /usr/share/nginx/html/config.php
sudo systemctl restart php-fpm nginx
```

### 9. Test
Open `http://EC2_PUBLIC_IP` in a browser, submit the form, then verify:

```bash
mysql -h RDS-ENDPOINT -u admin -p
USE studentdb;
SELECT * FROM students;
```

## Screenshots

| | |
|---|---|
| Registration form | `screenshots/registration-<img width="1366" height="768" alt="form" src="https://github.com/user-attachments/assets/4662dade-3bc7-4ac4-9d71-38db7d6d4a66" />
` |
| Successful submission | `screenshots/registration-<img width="1366" height="768" alt="success" src="https://github.com/user-attachments/assets/cab87d03-92bf-405c-b608-5f4538613c5f" />
` |
| Data verified via SSH (MySQL shell) | `screenshots/mysql-<img width="1366" height="768" alt="verify" src="https://github.com/user-attachments/assets/50dd8ccb-39cb-4abc-b265-20422a084013" />
` |
| EC2 instance running | `screenshots/ec2-<img width="1366" height="768" alt="instance" src="https://github.com/user-attachments/assets/634782ad-7cc6-46fa-8697-1b2e906e437c" />
` |
| RDS database available | `screenshots/rds-<img width="1366" height="768" alt="database" src="https://github.com/user-attachments/assets/876be84f-1085-4e18-bd37-2ab208970bc4" />
` |
# 👨‍💻 Author

**Aswad Gorivae**

AWS Cloud / DevOps Project

## Tech stack
AWS EC2 · AWS RDS (MySQL) · Nginx · PHP-FPM · HTML/CSS
