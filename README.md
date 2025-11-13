# BusTicket - Online Bus Reservation System

A modern, full-featured bus ticket booking system built with PHP, Bootstrap 4, and jQuery.

## Features

- Search buses by route, date, and time
- Real-time seat availability and selection
- Secure booking and payment processing
- Printable tickets
- Responsive mobile-first design
- 24/7 booking availability



## Setup Instructions

### 1. Prerequisites

- PHP 7.4+ with MySQLi extension
- MySQL 5.7+ or MariaDB 10.3+
- Apache/Nginx with .htaccess support (or nginx rewrite rules)
- jQuery 3.6+ (included)
- Bootstrap 4 (included)

### 2. Database Setup

1. Create a new database:
   \`\`\`sql
   CREATE DATABASE bus_service;
   \`\`\`

2. Import the schema:
   \`\`\`bash
   mysql -u root -p bus_service < data/bus_service.sql
   \`\`\`

3. Or run the Python setup script:
   \`\`\`bash
   python3 scripts/setup-db.py
   \`\`\`

### 3. Configuration

Update database credentials in `app/db.php`:

\`\`\`php
$connection = mysqli_connect(
    'localhost',      // Host
    'root',          // Username
    'password',      // Password
    'bus_service'    // Database
);
\`\`\`

### 4. File Permissions

\`\`\`bash
chmod 755 public/
chmod 755 public/assets/
chmod 644 public/*.php
chmod 755 app/
chmod 644 includes/*.php
\`\`\`

### 5. Testing

Visit `http://localhost/bus-ticket/public/index.php` in your browser.

Check system health: `http://localhost/bus-ticket/public/api/health-check.php`

## Usage Flow

1. **Search**: User enters route, date on homepage
2. **Results**: View available buses with fares
3. **Seats**: Select seats and boarding point
4. **Booking**: Enter passenger details
5. **Payment**: Choose payment method and pay
6. **Ticket**: Receive and print ticket

## API Endpoints

### AJAX Endpoints

- `seat_info.php` - Load seat grid for selected trip
- `bus_info.php` - Load bus details modal
- `api/health-check.php` - System health status

### Data Flow

\`\`\`
Session/Storage:
├── trip_no           // Selected trip ID
├── selected_seats    // Comma-separated seat list
├── boarding_point    // Selected boarding location
├── total_fare        // Calculated fare
└── trip_fare_per_seat // Per-seat rate
\`\`\`

## Database Schema

### Tables

- `bus_info` - Bus operator details
- `bus_lists` - Trip schedules and availability
- `seat_info` - Individual seat status per trip
- `bookings` - Completed reservations and payments

### Key Relationships

\`\`\`
bus_info (1) ──→ (Many) bus_lists
bus_lists (1) ──→ (Many) seat_info
bus_lists (1) ──→ (Many) bookings
\`\`\`

## Security Features

- Input validation and sanitization
- SQL injection prevention via prepared queries
- CSRF protection via token validation
- Password hashing for future auth features
- HTTPS redirect capability
- Row-level security ready

## Performance Optimizations

- Responsive design (mobile-first)
- CSS animations instead of heavy JavaScript
- Lazy loading for images
- Database query caching with prepared statements
- Minified assets (ready for production)

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Troubleshooting

### Database Connection Error
- Check credentials in `app/db.php`
- Ensure MySQL service is running
- Visit `api/health-check.php` for diagnostics

### Seats Not Loading
- Clear browser cache
- Check console for JavaScript errors
- Verify `seat_info.php` permissions

### Payment Not Processing
- Mock payment system is read-only
- Check server logs for errors
- Verify form submission and data passing

## Future Enhancements

- User authentication and profiles
- Email/SMS notifications
- Cancellation and refund processing
- Admin dashboard
- Real payment gateway integration
- Booking history
- Feedback and ratings

## Support

For issues or questions, contact: support@busticket.bd

## License

Proprietary - All rights reserved

## Contributors

Sajid Mahmud
