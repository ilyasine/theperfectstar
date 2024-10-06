<?php 

/**
 * The template for displaying Student Access Page
 *
 * Template Name: Student Access Page
 * 
 * This is the template that displays the Student Access Page.
 *
 * @package TPRM_Theme
 */

get_header(); ?>

hhhhhhhhhh

<div class="student-access-page-container">
    <?php
    // Get the student_id from the URL
    $student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;

    if ($student_id) {
        // You can customize the content here based on the student_id
        // Fetch student information or display the login form

        // Example: Display the student ID
        echo '<h2>Student Access for ID: ' . esc_html($student_id) . '</h2>';

        // Insert your form or any other content here
        ?>
        <form id="student-access-form" method="POST">
            <label for="student_password">Enter Password:</label>
            <input type="password" id="student_password" name="student_password" required>
            <button type="submit">Login</button>
        </form>
        <?php
    } else {
        echo '<p>No valid student ID provided.</p>';
    }
    ?>
</div>

<?php get_footer();
