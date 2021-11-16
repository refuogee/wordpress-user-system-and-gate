# wordpress-user-system-and-gate
A user system and content gating system designed for someone running courses via their WordPress website.

This was pasted into the functions.php file of the website described.

1. I created a Contact Form 7 form to get new student details including which course the signed up for - these were input by an admin.
2. From this input a new user was created with a role based on the courses offered. According to whichever role was chosen they could then view restricted contant for that course.
3. Seeing as Woocommerce was installed I used their account system as a student's portal. They would go to 'myaccount' and see the courses they had access to
4. I also used custom fields to indicate if a page was 'restricted' and then which course it belonged to
5. Using hooks for the main content I checked if the content was restricted and if the current user was able to see it.
6. If not they got an error page displayed to them. This error page was just a wordpress page built in wordpress etc and then displayed.
7. If it all checked out a user would see the content they were meant to see.
8. Another portion of this was to remove the need for users to input details into assessment forms each time they completed one.
9. I took the user details and pushed them to the DOM - these were then pulled and inserted into hidden contact form 7 fields that I had used to build assessments (not shown in this code)
10. A final thing to add is that I did rename various functions as to hide my employers and the clients details as much as possible. Therefore functions would have been named with the company name first - as to indicate our work but then also in the hopes of not clashing with any other code that might be present
11. 

