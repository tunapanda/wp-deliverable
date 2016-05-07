# wp-deliverable
Wordpress plugin that lets learners submit deliverables and have coaches review them. 

## How to setup
To install the plugin just clone down this repository into your wordpress plugins repository. You will then need to activate the plugin in the wordpress plugins menu. 

## How it works
After installation a Delivarables options is created in the admin menu. </br>
The menu provide three menu items</br>
####1) Manage delivarables</br>
This is where you can add a new deliverable. The image below illustrate how this works. 
![delivarables](https://github.com/tunapanda/wp-deliverable/blob/master/img/delivarables.png)
Note:</br> 
* The review group is created in the [Groups](https://wordpress.org/plugins/groups/) plugin. I had already created a programmers group. </br>
* A delivarable in form of a  url, zip file or a PDF.
* Targeted users are content/knowledge creators.</br>

####2) Review submissions
This is where the teacher/reviewer checks any submitted work. Users only see submitted items if they are part of a review group that the work was targeted to. </br>

####3) xAPI settings
This plugin sends [statements](https://tincanapi.com/statements-101/) to a Learning Record Store(LRS). We use [Learning Locker](https://learninglocker.net/) as our LRS. If you have an LRS setup just input the xAPI endpoint, usename and password.

After you have created a delivarable, it needs to be visible to the learneres. This is achieved by including the following shortcode.
![deliv-shortcode](https://github.com/tunapanda/wp-deliverable/blob/master/img/deliv-shortcode.png). </br>A delivarable ends up being a swagifact. 

This is what it looks like to the learner.
![deliv-front](https://github.com/tunapanda/wp-deliverable/blob/master/img/deliv-front.png)
