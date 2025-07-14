#  Eventified – Event Management System

**Eventified** is a full-featured PHP-based web application where users can explore and register for events, and organizers can create, manage, and engage attendees through polls, forums, and Q&A.

> 💡 For styling, Tailwind CSS is loaded via CDN.  
> ⚠️ Stripe keys are removed for security. Use your own credentials or contact the maintainer.

---

## 🚀 Features

| Feature        | Users                        | Organizers                                   |
|----------------|------------------------------|-----------------------------------------------|
| Authentication | Register, Login              | Register, Login                               |
| Dashboard      | View purchased tickets       | View events, statistics, revenue              |
| Events         | Browse & buy tickets         | Create, edit, delete events                   |
| Engagement     | Join forum, vote, ask Qs     | Create polls, manage forum/Q&A                |
| Payments       | Mock ticket purchase         | View ticket sales                             |

---

## 🛠 Tech Stack

- **Frontend**: HTML, Tailwind CSS (via CDN)  
- **Backend**: Core PHP  
- **Database**: MySQL  
- **Server**: Apache (XAMPP recommended)

---

## ⚙️ Getting Started (with XAMPP)

1. **Clone this repo** to:  
   `C:\xampp\htdocs\event-system`

2. **Launch phpMyAdmin**

3. **Create a database**:  
   `event_management_system`

4. **Import SQL dump** (provided in the project)

5. **Run the app** in browser:  
   [http://localhost/event-system/](http://localhost/event-system/)

6. Register a new account or use demo credentials (if available)

---

## 📦 Manual Library Setup

Since Composer was unavailable during development:

- ✅ **Stripe SDK** – Manually included for mock payment integration  
- ✅ **PHP QR Code Generator** – Included manually  
- ❌ **PHPMailer** and **PDF Generator** – Not included (can be added later)

---

## 📁 Folder Structure

event-management-system/
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── components/
│   ├── header.php
│   └── footer.php
├── dashboard/
│   ├── user_dashboard.php
│   └── organizer_dashboard.php
├── engagement/
│   ├── forum.php
│   ├── qna.php
│   ├── poll.php
│   ├── manage_poll.php
│   ├── manage_qna.php
│   ├── manage_forum.php
│   ├── event_qna.php
│   └── create_poll.php
├── events/
│   ├── create_event.php
│   ├── edit_event.php
│   ├── delete_event.php
│   ├── event_details.php
│   ├── list_events.php
│   ├── attendees.php
│   └── buy_ticket.php
├── payments/
│   ├──create_checkout_session.php
│   ├──payment.php
│   ├──success.php
├── includes/
│   └── db.php
├── index.php
├── assets/ 
│   ├── images/     
├── config.php  
└── README.md         # Project setup instructions



---

## 🧱 Database Schema Overview

- **users**:  
  `id`, `name`, `email`, `password`, `role` (`user` / `organizer`)

- **events**:  
  `id`, `title`, `description`, `datetime`, `location`, `category`, `privacy`, `ticket_info`

- **tickets**:  
  Links users to events with `ticket_type` and `price`

- **discussions**:  
  Forum messages tied to events

- **polls**, **poll_options**, **poll_votes**:  
  Polls created by organizers and corresponding votes

- **qna**:  
  Questions asked by users, answered by organizers

---

## 💳 Stripe Payment Notice

> ⚠️ **Stripe keys are removed**  
> To enable real payment processing:
> - [Create a Stripe account](https://dashboard.stripe.com/register)
> - Generate **Publishable** and **Secret** keys
> - Add them in `payments/create_checkout_session.php`

---

## ✅ Security & Access

- Session-based authentication
- Role-based route protection (user vs organizer)
- Event privacy settings supported

---

## 📌 Known Limitations

- 📧 Email & PDF features not implemented (due to missing Composer setup)
- 🖼 Image uploads for events are optional and stored locally
- 🔐 Production security features (CSRF, XSS) not fully enforced

---

## 🎯 Future Enhancements

- Integrate **PHPMailer** for email notifications  
- Add **PDF Ticket Generation**  
- Add **event reminders via email or SMS**

---

## 📬 Feedback / Support

For questions, feedback, or Stripe credentials, feel free to reach out!

Happy browsing! 🚀
