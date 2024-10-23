<?php
    include 'header.php'; // Include header
?>

<style>
/* General Styles for About Us Page */
.about-us {
    padding: 50px 0;
    background-color: #f9f9f9;
}

.about-us h2 {
    font-size: 32px;
    margin-bottom: 20px;
    color: #333;
    text-transform: uppercase;
}

.about-us p {
    font-size: 18px;
    line-height: 1.8;
    color: #555;
    margin-bottom: 20px;
}

.about-us ul {
    list-style: none;
    padding: 0;
}

.about-us ul li {
    font-size: 18px;
    margin-bottom: 10px;
}

.about-us ul li strong {
    color: teal;
}

/* Our Story Section */
.about-us .story-img {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 20px;
}

/* Our Vision & Values Section */
.about-us .vision-img {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 20px;
}

/* Our Team Section */
.about-us .team-img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 15px;
}

.about-us .team-member {
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .about-us h2 {
        font-size: 28px;
    }

    .about-us p {
        font-size: 16px;
    }

    .about-us .team-img {
        width: 120px;
        height: 120px;
    }
}
</style>

<section class="about-us">
    <div class="container">
        <!-- Our Story Section -->
        <div class="section" id="our-story">
            <h2 class="text-center">Our Story</h2>
            <img src="../../image/background/ourstory.webp" alt="MiMie Burger Shop" class="img-fluid story-img">
            <p>
                MiMie Burger started with a simple passion – a love for crafting delicious burgers from fresh, high-quality ingredients. 
                From our humble beginnings as a small burger joint, we have grown into a beloved local favorite, known for our unique 
                burger combinations and commitment to great taste. We believe that every bite should be an experience, and that’s why we 
                focus on delivering flavors that are as memorable as they are mouthwatering.
            </p>
        </div>

        <hr>

        <!-- Our Vision & Values Section -->
        <div class="section" id="our-vision">
            <h2 class="text-center">Our Vision & Values</h2>
            <img src="../../image/background/ingredients.webp" alt="Fresh Ingredients" class="img-fluid vision-img">
            <p>
                At MiMie Burger, our vision is to create a place where people can come together and enjoy great food in a welcoming, 
                friendly atmosphere. We value:
            </p>
            <ul>
                <li><strong>Quality Ingredients:</strong> We use only the freshest, locally sourced ingredients to ensure every burger is top-notch.</li>
                <li><strong>Customer Satisfaction:</strong> Our customers are at the heart of everything we do. Your feedback drives us to improve every day.</li>
                <li><strong>Innovation:</strong> We love experimenting with flavors and creating new, exciting burgers for our menu.</li>
                <li><strong>Community:</strong> As a local business, we are proud to support our community and give back whenever we can.</li>
            </ul>
        </div>

        <hr>

        <!-- Our Team Section -->
        <div class="section" id="our-team">
            <h2 class="text-center">Meet Our Team</h2>
            <div class="row text-center">
                <div class="col-lg-4 col-md-6 col-sm-12 team-member">
                    <img src="../../image/profile/ceo.jpg" alt="CEO" class="team-img img-fluid">
                    <h4>MiMie Wynn</h4>
                    <p>Founder & CEO</p>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 team-member">
                    <img src="../../image/profile/headchef.jpg" alt="Head Chef" class="team-img img-fluid">
                    <h4>Zuji Xero</h4>
                    <p>Head Chef</p>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 team-member">
                    <img src="../../image/profile/manager.jpeg" alt="Operations Manager" class="team-img img-fluid">
                    <h4>Chero Ripa</h4>
                    <p>Operations Manager</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
    include 'footer.php'; // Include footer
?>
