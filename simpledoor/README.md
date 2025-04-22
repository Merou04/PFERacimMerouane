# Simple Door Project

This project is a simple web application that simulates a door with four states: Open, Half Open, Out Only, and Closed. The user can interact with the door through a button that changes its state, which is reflected visually by four lights. The current state of the door is stored in a database, allowing for multiple doors to be managed.

## Project Structure

- **index.html**: Contains the HTML structure for the simple door interface, including a button and four lights.
- **style.css**: Defines the CSS styles for the HTML elements, controlling the appearance of the button and lights.
- **script.js**: Handles the button click event, sends an AJAX request to update the door state in the database, and updates the visual representation of the lights based on the current state.
- **db_connect.php**: Establishes a connection to the database for use in other PHP files.
- **update_state.php**: Updates the door state in the database based on the door ID and new state received from the AJAX request.
- **get_state.php**: Retrieves the current state of the door from the database and returns it as a response to the AJAX request.
- **database.sql**: Contains SQL commands to create the necessary database and table structure for storing door states.
- **README.md**: Documentation for the project, including setup instructions and an overview of how the application works.

## Setup Instructions

1. **Database Setup**:
   - Create a database using the SQL commands provided in `database.sql`.
   - Ensure that the database connection details in `db_connect.php` are correctly configured.

2. **File Structure**:
   - Place all files in a directory accessible by your web server.

3. **Accessing the Application**:
   - Open `index.html` in a web browser to interact with the door simulation.

## How It Works

- The user clicks the button to change the state of the door.
- Each click cycles through the four states, which are visually represented by the lights.
- The current state is stored in the database, allowing for persistent state management across sessions.

## Future Enhancements

- Implement user authentication to manage multiple users and their doors.
- Add more states or features to the door simulation.
- Improve the user interface with animations and better visual feedback.