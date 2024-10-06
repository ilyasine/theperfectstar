<?php 

function course_exam_screen_content() {
    ?>

   <div id="learndash-content-final-quiz" class="learndash-group-final-quiz">
   <div class="card">
       <h1><b>Exam Section</b></h1>
       <div class="quiz-container">
           <?php 
           global $wpdb;

           $latest_timestamp = 0;
           $latest_group_id = '';
           $user_id = get_current_user_id();

           $user_groups  = learndash_get_users_group_ids($user_id);

           foreach ($user_groups as $group_id) {
               $meta_key = 'learndash_group_' . $group_id . '_enrolled_at';
               $enrolled_at = get_user_meta($user_id, $meta_key, true);

               // Check if the current enrollment timestamp is greater than the latest timestamp
               if ($enrolled_at > $latest_timestamp) {
                   $latest_timestamp = $enrolled_at;
                   $latest_group_id = $group_id;
               }
           }

           $slug = 'final-course-test';
           $post_type = 'sfwd-courses';
           $final_quiz_course = get_page_by_path( $slug, OBJECT, $post_type );
           if($final_quiz_course){
               $course_id = $final_quiz_course->ID;
               $get_quizzes = learndash_get_course_quiz_list($course_id);
               foreach($get_quizzes as $quizz){
                   $final_quiz_enable = get_post_meta($quizz['id'],'final_quiz_enable_'.$latest_group_id,true);
                   if($final_quiz_enable == 'yes' ){
                       $quizzQuetions = get_post_meta($quizz['id'],'final_quize_group_'.$latest_group_id,true);
                       $page_slug = $quizz['post']->post_name;
                       $site_url = get_home_url();
                       $user_quiz_has_completed = learndash_user_quiz_has_completed($user_id, $quizz['id'],$course_id );
                       $quiz_has_completed = '';
                       if($user_quiz_has_completed)
                       {
                           $quiz_has_completed = 'completed';
                       }

                       $page_url = $site_url . '/' . '/my-course/'. $slug . '/quizzes/' . $page_slug;
                       echo '<a href="'.$page_url.'"><div class="quiz-card '.$quiz_has_completed.'">
                               <b>'.$quizz['post']->post_title.'</b><br>
                               <span>Questions: '.count($quizzQuetions).'</span>
                          </div></a>';
                   }
               }
           }else{
               echo '<div class="quiz-card">
                       <b>No Quiz Found</b><br>
                  </div>';
           }
           ?>
       </div>
   </div>
</div>

<STYle>
   #learndash-content-final-quiz {
   display: flex;
   justify-content: center;
   align-items: center;
   padding: 15px;
}

.card {
   background: #f8f8f8;
   border-radius: 8px;
   box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
   padding: 20px;
   width: 100%;
}

.card h1 {
   font-size: 24px;
   color: #333;
   margin-bottom: 20px;
   text-align: center;
}

.quiz-container {
   display: flex;
  
   align-items: center;
   flex-wrap: wrap;
}

.quiz-card {
   background: #ddd;
   border-radius: 8px;
   padding: 15px;
   margin: 10px;
   text-align: center;
   font-size: 18px;
   transition: background-color 0.3s ease;
  
   box-sizing: border-box;
}

.quiz-card.completed {
   background-color: #8dd553 !important;
   WIDTH: AUTO;
}

.quiz-card:hover {
   background-color: #ccc;
   cursor: pointer;
}

.quiz-card b {
   font-size: 20px;
   display: block;
}

.quiz-card span {
   font-size: 16px;
   color: #666;
}

</STYle>
   
   <?php
}