<!DOCTYPE html>
<html lang="en">
<head>
<?php wp_head();  //! include this to link stylesheets and scripts 
?>
</head>

<?php
//acces the db and set the post variables for
    global $wpdb;
    $ID = get_the_ID();
    $ourDB = $wpdb->prefix.'lms_course_details';
    $title = get_the_title();
    $subtitle = $wpdb->get_var("SELECT `subtitle` FROM `$ourDB` WHERE `ID` = $ID ");
    $price = $wpdb->get_var("SELECT `price` FROM `$ourDB` WHERE `ID` = $ID ");
    $video = $wpdb->get_var("SELECT `video` FROM `$ourDB` WHERE `ID` = $ID ");
    $curriculum = $wpdb->get_var("SELECT `curriculum` FROM `$ourDB` WHERE `ID` = $ID ");
    
    //separate the string into an array using the "-*-" as divider
    $exploded = explode("-*-", $curriculum);

    //get the video id from the link, and use it in embeded way inside iframe src=""
    $updatedLink = 'https://www.youtube.com/embed/'.substr($video,strrpos($video,"=")+1);

    //get all of the course titles from ourdb to display in the More Products section
    $IDs_arr = $wpdb->get_col("SELECT `id` FROM `$ourDB`")
?>

<body class="bodyTemplate">
    <div class="courseContainer">
        <div class="topTemplate">
            <h3><?php echo $title ?></h3>
            <p><?php echo $subtitle ?>
            <p><?php echo $updatedLink ?>
            </p>

        </div>

        <div class="mainTemplate">
            <div class="leftSide">
                <h1>LEFT SIDE TESTING</h1>
                <iframe width="100%" height="300px" src="<?php echo $updatedLink ?>" frameborder="0" allow="autoplay" allowfullscreen origin="crossorigin"></iframe>
            </div>

            <div class="rightSide">
                <h4>RIGTH SIDE TESTING</h4>
                <p style="font-weight:900;">Price only: $<?php echo $price?></p>
                <button id="button" style="width:50%; ">Get Started</button>
                <hr style="width:80%">
                <p>Lorem Impsum</p>
                <p>Lorem Impsum</p>
                <p>Lorem Impsum</p>
                <p>Lorem Impsum</p>
                <p>Lorem Impsum</p>
                <p>Lorem Impsum</p>
                <hr style="width:80%">
            </div>    
        </div>

        <div class="bottomTemplate">
            <p><?php echo get_the_content() ?></p>
            <image style="width:100px; " src="<?php echo plugin_dir_url(__DIR__).'/assets/wp_m03.png'; ?>" ></image>       
            <h6>INSTRUCTOR</h6>    
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        </div>
        
        <div class="bottomTemplate">
            <h1>Curriculum</h1>
            <p><?php echo $curriculum?></p>
            
            <?php if (count($exploded)>0)
            {
                $i=0;
                while(count($exploded)>$i){
                    ?>
                    <a href="#">Section <?php 
                        echo ($i+1).":". $exploded[$i];
                        $i+=1;
                        ?>
                    </a>
                    
                <?php }; } ?>
        </div>
        
        <div class="bottomTemplate">
            <h1>Other Products</h1>
            <div class='cards'>  
            <?php 

            if (count($IDs_arr)>0)
            {
                $i=0;
                while(count($IDs_arr)>$i && $i<4){
                    if ( $i > 0 )
                    {   
                        echo "<div class='card'> ";
                        echo ("<image src='".get_the_post_thumbnail_url($IDs_arr[$i])."'/>");
                        echo ("<p>".get_the_title($IDs_arr[$i])."</p>");
                        
                        echo "</div>";
                    }
                        
                        
                    $i++;
                }
            }
            ?>
            </div>
        </div>    

    <script type="text/javascript">
        const button = document.getElementById("button");
        button.addEventListener('click',()=>{
            elapsedTime = 0;
        })
        //loop

        button.innerHTML = "Hello JavaScript!";
        let elapsedTime = 0;
        function loop(){
            requestAnimationFrame(loop);
            elapsedTime+=1/60;
            button.innerHTML = Math.round(elapsedTime)+" s" 
        }
        loop();

    </script>
</body>

</html>