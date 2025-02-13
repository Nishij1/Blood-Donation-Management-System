# Blood Donation Management System

## Overview

The Blood Donation Management System is a web application designed to connect blood donors with recipients in need of blood. The system allows users to register as donors or recipients, manage blood requests, and track blood inventory. Admins can manage users, view statistics, and oversee the entire operation of the system.

## Features

- **User Registration**: Users can register as donors or recipients.
- **Admin Panel**: Admins can manage users, view statistics, and handle blood requests.
- **Blood Inventory Management**: Track available blood units and their types.
- **Contact Form**: Users can contact the organization for inquiries.
- **Responsive Design**: The application is mobile-friendly and works on various devices.

## Technologies Used

- **Frontend**: HTML, CSS, Bootstrap, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Libraries**: 
  - Bootstrap for responsive design
  - Font Awesome for icons
  - AOS (Animate On Scroll) for animations

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yourusername/blood-donation-management-system.git
   cd blood-donation-management-system
   ```

2. **Set up the database**:
   - Create a new MySQL database (e.g., `blood_donation1`).
   - Import the `database.sql` file to create the necessary tables.

3. **Configure the application**:
   - Update the database connection settings in `config/config.php`.

4. **Run the application**:
   - Start a local server (e.g., XAMPP, WAMP, or MAMP).
   - Place the project folder in the server's root directory (e.g., `htdocs` for XAMPP).
   - Access the application via your web browser at `http://localhost/blood-donation-management-system/index.php`.

## Usage

- **User Registration**: Users can register as donors or recipients.
- **Admin Login**: Admins can log in using the credentials created in `create_admin.php`.
- **Dashboard**: Admins can view statistics, manage donors and recipients, and handle blood requests.

## Sample Data Generation

To generate sample data for testing, navigate to `admin/generate_sample_data.php` in your browser. This will create 30 random users and 10 blood requests.

## Contributing

Contributions are welcome! If you have suggestions for improvements or new features, please open an issue or submit a pull request.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contact

For any inquiries, please contact:
- **Email**: nishijsontakke123gmail.com