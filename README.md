Photo ABCD website documentation.


To make the local server display newly uploaded files in index.php & my-blogs.php:

	1. The "Image" folder needs to gain read & write access, and ownership of the file
	   needs to change so that the PHP process is running under the same user who owns
	   the directory.
	2. If you're using XAMPP, the Apache process might be running under a system account.
	   This can be checked using the following command in the terminal:
		
		ps aux | grep httpd

	3. Look for a process owned by a user (such as _www or daemon). If it's a system 
	   user, change the directory ownership to match by using the following command:

		sudo chown -R (system_user):staff path/to/photo_abcd_A/images

	4. After running the two previous commands, retry using the website using the local
	   server. It should now be functioning properly.


                
    Homepage
The homepage provides functionality to sort, search and view blogs.
You can choose to view the blogs a certain way by changing the view type under the "View" dropdown. 

For sorting options you are able to sort alphabetically or by the event dates of the blogs both either in ascending or descending order.

    Logging in
When logging in you are given the option to register if you do not have an account. 
When registering you will be prompted to enter required information
such as email and password.

    Logged in as standard user
Upon logging in you will be shown any blogs that are created by you.
As a logged in user you are also capable of changing account settings such as your first and last name, profile picture and password.

    Logged in as an admin user
When logged in as an admin you are given access to a new administration page on the nav bar. This page displays 
user, blog and site information to you.

    alphabet book
users are able to compile a "book" of blog posts with alphabetical entries with the aim completing all 26 letters of the alphabet.
You are able to have multiple blog entries under the same letter you selected. So each letter can have mutliple blog posts.
There is also a status bar that determines your progress on completing the alphabet book based on how much letters you have filled in. 
You can edit the title of the book as well as print it to PDF format.



                HOW TO's

    How to upload a blog
When logged in, you will be able to post a new blog.
Blog posts require a title to start with either a letter or number (no characters) and require the description field to be filled.

You will be able to upload an image or gif of your choice but it is not required. If an image is not uploaded, a default image will be put in its place instead.

You will also be able to add a link to a YouTube video. This can be added on it's own, or can be added in addition to images or gifs.

You will then need to select the Event date with the calendar icon or you can enter the date manually. 

You are then able to toggle if you want the blog to appear publicly to everyone or if you want it to be private. 


    How to create an alphabet book
You must be logged in as either a user or an admin to create an Abook
This allows you to create a book of blog posts, based off of the alphabet.
Whenver a new letter of the alphabet is filled out with a blog (or multiple blogs)
a progress bar appears showing how much you have completed.

You are able to print your book to pdf, as well as edit the book as well.



