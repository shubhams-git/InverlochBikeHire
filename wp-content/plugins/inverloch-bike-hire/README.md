
README.md

Serves as the plugin’s manual, offering comprehensive guidance on its installation, features, and usage. This document is essential for both users and developers, providing a quick reference to understand and leverage the plugin effectively.



Inverloch Bike Hire Management System: File Structure Plan 

File Structure Overview
The proposed structure is meticulously organized into several directories, each serving a specific role within the plugin's architecture:


Directory Breakdown and Justification

/admin

The /admin directory is crucial for managing the administrative interface of the plugin. It organizes all backend functionalities, ensuring a clean separation from the front-end logic.

•	admin-init.php: Handles the initialization of admin-specific features, including enqueuing CSS and JavaScript files that are exclusive to the admin dashboard. This central point for admin-related scripts and styles simplifies management and updates.

•	admin-menus.php: Responsible for setting up the plugin’s navigation within the WordPress admin area. It uses WordPress functions to create a coherent and accessible menu structure, directly impacting the user experience for administrators.

•	/pages: Houses the PHP files for rendering content on each admin page. Organizing these pages into a dedicated directory allows for straightforward navigation and editing of individual administrative interfaces, each tailored to a specific aspect of the plugin's functionality.



/includes

This directory forms the backbone of the plugin, encapsulating its core logic and database interactions.

•	/models: Embraces an Object-Oriented approach, with each model class representing a database table. This method encapsulates data access and manipulation logic, making the code more modular, easier to understand, and maintain.

•	db-operations.php: Dedicated to setting up the plugin’s custom database structure. It includes the SQL statements for table creation, ensuring the plugin’s data layer is correctly initialized and ready for operation upon plugin activation.

•	form-handlers.php: Interacts with the models to process and validate data submitted from the admin forms. By centralizing form handling in this file, the plugin ensures data consistency and security across all admin interfaces.

•	plugin-activator.php / plugin-deactivator.php: Define tasks for setting up and tearing down the plugin’s environment. This includes creating or removing database tables, making these operations critical for preserving data integrity and plugin functionality.


/public

Targets the public-facing side of the plugin, handling how content is displayed and interacted with by end-users.

•	public-init.php: Initializes public features, preparing the plugin's front-end aspects such as enqueuing stylesheets and JavaScript for site visitors.

•	shortcodes.php: Defines shortcodes, allowing users to easily embed dynamic content related to the plugin within posts and pages. This file plays a crucial role in extending the plugin’s functionality to the front-end without requiring direct theme modifications.

•	/templates: Contains PHP templates for the plugin’s front-end displays. This structured approach to template management allows for flexible content presentation, enabling custom layouts and styles for displaying inventory, reservations, and more.


/assets

Organizes the plugin’s static resources, such as CSS and JavaScript files, into a clear structure. Dividing assets into /css and /js, further separated into admin and public categories, ensures that resources are loaded only where needed, optimizing performance.


inverloch-bike-hire-management.php

The main plugin file acts as the heart of the plugin, including the WordPress plugin header and initiating the plugin's core functionalities. It requires other PHP files from the plugin and registers hooks for activation, deactivation, and initialization.

