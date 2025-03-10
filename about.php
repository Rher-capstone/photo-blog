<?php
$CURRENT_PAGE = "About";
session_start();
include_once("includes/db-conn.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include("includes/head-tag-contents.php");?>
    <link rel="stylesheet" href="css/about.css">
</head>

<body>
    <?php include("includes/top-bar.php");?>
    <h2>About Us</h2>

        <div class="container" id='main-body'>

            <div class="about-entry left">
                <h1>Cassie Sand</h1>
                <div>
                    <div class="img-wrap">
                        <img src="images/about/cassie.jpg" alt="team image">
                    </div>

                    <div class="desc">
                        <p>
                        Cassie Sand was raised in Lakeville, Minnesota. After graduating from Lakeville North High School, she attended Simpson College located in Indianola, Iowa.
                        While in Iowa, she completed a double major in Business Management and Theatre Arts, graduating in 2020.
                        Upon entering the workforce, she chose to complete a second bachelors degree to pursue a career in software development.
                        She will be graduating in December 2024 with a Bachelor of Science degree in Computer Science from Metroplitan State University.
                        Her well-rounded background has proven essential to developing new creative solutions.
                        </p>
                    </div>
                </div>
            </div>

            <div class="about-entry right">
                <h1>Samuel Goergen</h1>
                <div>
                    <div class="desc">
                        <p>
                            Sam is originally from Plymouth, Minnesota. After high school, he excelled in a sales career, generating around one million dollars in annual revenue for five consecutive years.
                            More recently, he's pivoted to software engineering, captivated by its potential to transform business and interactions.
                            In his free time, he likes to indulge in golf and frisbee golf, stay fit with weightlifting, and relax by watching football.
                            His passion for achievement shines through in everything he does, from coding to sports.
                        </p>
                    </div>

                    <div class="img-wrap">
                        <img src="images/about/sam.jpg" alt="team image">
                    </div>
                </div>
            </div>

            <div class="about-entry left">
            <h1>Justin Her</h1>
                <div>
                    <div class="img-wrap">
                        <img src="images/about/justin.jpg" alt="team image">
                    </div>
                    
                    
                    <div class="desc">
                        <p>
                            Justin is a Minnesota native with a long standing relationship with computers and technology since his youth. As such,
                            after graduating from Eastern Gateway Community College in 2021, he sought to attain a Bachelor's Degree in Computer Science
                            and is set to graduate from Metropolitan State University in December, 2024. He is a member of Metrostate's Hmong Student 
                            Organization, and has participated in various activities with other student organizations. He is an avid PC gamer with experience
                            in game development (Unity) and modding.
                        </p>
                    </div>
                </div>
            </div>

            <div class="about-entry right">
            <h1>Ryan Her</h1>
                <div>
                <div class="desc">
                    <p>
                        Ryan was born and raised in Saint Paul, Minnesota. He attended Blaine High School and graduated in 2019. Ryan attended Anoka Ramsey Community College and
                        then transferred to Metropolitan State University, majoring in Computer Science and is set to graduate in December 2024 with his Bachelors. In his free time
                        Ryan enjoys training in Muay Thai and watching Vikings football. Ryan has successfully completed a Software Engineering Internship with Securian Financial and aims 
                        to continue on this career path in tech.
                    </p>
                </div>

                    <div class="img-wrap">
                        <img src="images/about/ryan.png" alt="team image">
                    </div>

                </div>
            </div>

            <div class="about-entry left">
            <h1>Logan Johansson</h1>
                <div>
                    <div class="img-wrap">
                        <img src="images/about/logan.jpg" alt="team image">
                    </div>
                    
                    <div class="desc">
                        <p>
                            Logan was born in Wisconsin and attended UW-Madison majoring in Biology. Logan moved to Minnesota nearly 10 years ago to pursue a career opportunity, and has been 
                            residing in the state ever since. After gaining valuable experience in the workforce, Logan decided to further his education and pursue a bachelor's degree in Computer Science at 
                            Metropolitan State University. Logan is set to graduate in December 2024 and is excited to continue building his career in the tech field.
                        </p> 
                    </div>
                </div>
            </div>

        </div>

    <?php include("includes/footer.php");?>
</body>

</html>