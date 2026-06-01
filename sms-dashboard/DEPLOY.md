# SMS Dashboard — Deployment

## 1. Set Your Password

Edit `config.php` **before** copying files to the server:

```php
define('DASHBOARD_PASS', password_hash('YOUR_REAL_PASSWORD', PASSWORD_BCRYPT));
```

---

## 2. Copy Files to the Server

```bash
scp -r sms-dashboard/ root@<server-ip>:/opt/sms-dashboard
```

Or rsync:

```bash
rsync -av sms-dashboard/ root@<server-ip>:/opt/sms-dashboard/
```

---

## 3. Create the Logs Directory + Set Permissions

SSH into the server, then:

```bash
mkdir -p /opt/sms-dashboard/logs
chown -R apache:apache /opt/sms-dashboard
chmod 750 /opt/sms-dashboard/logs
```

> Use `ps aux | grep httpd` to confirm the Apache user — it's usually `apache` on Rocky Linux.

---

## 4. Install the Apache VHost Config

```bash
cp /opt/sms-dashboard/sms-dashboard.conf /etc/httpd/conf.d/sms-dashboard.conf
```

Add `Listen 8080` to `/etc/httpd/conf/httpd.conf` if it isn't there:

```bash
echo "Listen 8080" >> /etc/httpd/conf/httpd.conf
```

---

## 5. Open the Firewall Port

```bash
firewall-cmd --add-port=8080/tcp --permanent && firewall-cmd --reload
```

---

## 6. Reload Apache

```bash
systemctl reload httpd
```

---

## 7. Visit the Dashboard

Open a browser on your local network:

```
http://<server-ip>:8080
```

Login with `admin` / the password you set in step 1.

---

## File Structure on Server

```
/opt/sms-dashboard/
├── config.php        ← credentials + script whitelist
├── index.php         ← login page
├── dashboard.php     ← main UI
├── run.php           ← script runner (streams output)
├── logs.php          ← log listing + viewer
├── logout.php        ← destroys session
└── logs/             ← one .log file per run (blocked from direct HTTP)
```
