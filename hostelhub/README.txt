# HostelHub – Cloud-Based Hostel Management System

## Technology
PHP 8+, Apache, MySQL 8+, Bootstrap 5.3. The intended AWS deployment is Amazon EC2 for the PHP website and Amazon RDS for MySQL.

## Features
- Student registration and login
- Role-based student/admin dashboards
- Room listing and admin room CRUD
- Student room allocation requests
- Admin booking approval/rejection
- Student complaint submission
- Admin complaint status updates and deletion
- Prepared SQL statements, password hashing, output escaping, CSRF tokens and session ID regeneration

## 1. Create the database
1. Create an Amazon RDS MySQL database named `hostelhub`.
2. Connect to it using MySQL Workbench or the MySQL command line.
3. Run all statements in `database.sql`.

## 2. Configure the application
Edit `config.php`:
- Replace `YOUR_RDS_ENDPOINT` with the RDS endpoint (hostname only; do not include `https://` or a port).
- Set the RDS database username and password.
- Keep the database password private. Do not upload config.php to a public Git repository.

## 3. Create an administrator
1. Register a normal account using the website.
2. Connect to the database and run this query, replacing the email:
   `UPDATE users SET role='admin' WHERE email='admin@example.com';`
3. Log out and log in again. The account will now have the admin dashboard.

## 4. Local testing (optional)
Install XAMPP, copy the `hostelhub` folder into `C:\xampp\htdocs\`, configure `config.php` for local MySQL, start Apache and MySQL, import `database.sql` in phpMyAdmin, and open:
`http://localhost/hostelhub/`

## 5. AWS EC2 deployment (Ubuntu example)
Connect to the EC2 instance over SSH and run:
```bash
sudo apt update
sudo apt install apache2 php libapache2-mod-php php-mysql unzip -y
sudo systemctl enable --now apache2
```
Upload and extract the project into `/var/www/html/hostelhub/`. For example, upload `hostelhub.zip` with SCP, then:
```bash
cd /var/www/html
sudo unzip ~/hostelhub.zip
sudo chown -R www-data:www-data /var/www/html/hostelhub
sudo systemctl restart apache2
```
If your ZIP extracts into a nested folder, move the inner files so that `index.php` is at `/var/www/html/hostelhub/index.php`.

Update `/var/www/html/hostelhub/config.php` with the RDS endpoint and credentials. Visit:
`http://EC2-PUBLIC-IP/hostelhub/`

## 6. AWS networking
- EC2 security group inbound: SSH TCP 22 from your own public IP only; HTTP TCP 80 from the internet for the practical demo. Add HTTPS TCP 443 only after configuring TLS.
- RDS security group inbound: MySQL TCP 3306 with source set to the EC2 security group ID, not `0.0.0.0/0`.
- Keep RDS private if possible. EC2 and RDS must be in compatible VPC networking.
- Do not expose the database to the whole internet.
- Use a strong database password. Restrict IAM permissions to the resources/actions needed.

## 7. Demonstration checklist
1. Open homepage using EC2 public IP.
2. Register a student account.
3. Log in as student.
4. View rooms and submit a room request.
5. View the request under My Bookings.
6. Submit a complaint and view it.
7. Promote a separate account to admin using the SQL query above.
8. Log in as admin; add, edit and delete a room.
9. Approve/reject a room request.
10. Update/delete a complaint.
11. Show the records in the RDS database.

## 8. Practical 9 write-up outline
**Aim:** To design and deploy a cloud-based Hostel Management System capable of performing database operations.
**Services:** Amazon EC2, Amazon RDS for MySQL, Security Groups.
**Operations:** INSERT (registration, room request, complaint), SELECT (login, room list, dashboard), UPDATE (room details, booking and complaint status), DELETE (room and complaint records; cancellation of pending booking).
**Result:** The web application is deployed on AWS and performs database operations through a PHP backend connected to Amazon RDS.

## 9. Practical 10 write-up outline
**Aim:** To analyze and implement a real-world cloud computing solution using AWS, focusing on innovation, scalability and security.
**Case study:** A hostel needs a centralized portal for student registration, room allocation and complaint handling.
**Innovation:** Online room requests, status tracking and centralized complaint management.
**Scalability:** EC2 instance type can be increased; an Application Load Balancer and Auto Scaling Group can be added for multiple EC2 instances; RDS can be scaled vertically and backed up.
**Security:** IAM least privilege, security groups, private RDS networking, password hashing, prepared statements, CSRF protection, session regeneration and HTTPS (when configured).
**Result:** A centralized cloud-hosted hostel management application is implemented using AWS services.

## Important notes / limitations
- This is an educational prototype, not a production hostel billing or occupancy system.
- Room capacity is displayed but simultaneous allocation/occupancy enforcement is not implemented. Admin should verify capacity before approving requests.
- When an approved booking is rejected later, the room status is not automatically reset; admin should update room availability manually.
- Add HTTPS before handling real personal data. Use a secrets manager or secure environment configuration for production credentials.
- Avoid entering real student personal information in a classroom demo.
