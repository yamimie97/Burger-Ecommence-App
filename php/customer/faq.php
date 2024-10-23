<?php
include 'header.php'; // Include the header
?>

<section class="faq-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 mt-4">
                <div class="card shadow-sm">
                    <div class="card-header text-center bg-info text-white">
                        <h3>Frequently Asked Questions (FAQ)</h3>
                    </div>
                    <div class="card-body">
                        <p>Here are some commonly asked questions about MiMie Burger Shop and our services:</p>
                        <ul>
                            <li>
                                <strong>1. How do I place an order?</strong>
                                <p>You can place an order by browsing through our menu on the website, selecting the items you'd like, and then proceeding to checkout to complete your order.</p>
                            </li>
                            <li>
                                <strong>2. Can I modify my order after placing it?</strong>
                                <p>Once an order is placed, it cannot be modified directly through the website. However, you can reach out to our customer support for assistance.</p>
                            </li>
                            <li>
                                <strong>3. How do I track my order?</strong>
                                <p>Once your order is placed and confirmed, you will receive an order tracking link or updates through your registered email.</p>
                            </li>
                            <li>
                                <strong>4. What payment methods do you accept?</strong>
                                <p>We accept various payment methods including credit/debit cards and stored payment options. More payment methods will be available soon.</p>
                            </li>
                            <li>
                                <strong>5. How can I give feedback on my order?</strong>
                                <p>You can submit feedback by logging into your account and navigating to the feedback section. We value your input!</p>
                            </li>
                            <li>
                                <strong>6. Can I save my delivery addresses?</strong>
                                <p>Yes, you can save multiple delivery addresses in the "My Account" section for future orders.</p>
                            </li>
                            <li>
                                <strong>7. How do I reset my password?</strong>
                                <p>If you need to reset your password, go to the account page, click on "Update Password".</p>
                            </li>
                            <li>
                                <strong>8. How do I contact customer support?</strong>
                                <p>You can reach out to our support team through the "Contact Us" page or by emailing us at info@mimieburger.com.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .faq-page {
        padding: 50px 0;
    }

    .card {
        border-radius: 10px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
    }

    .card-header {
        background-color: #006d6d;
        color: white;
        font-size: 24px;
        padding: 15px;
    }

    .card-body {
        padding: 20px;
    }

    .faq-page ul {
        list-style-type: none;
        padding: 0;
    }

    .faq-page ul li {
        margin-bottom: 15px;
    }

    .faq-page ul li strong {
        font-size: 18px;
    }

    .faq-page p {
        font-size: 16px;
    }

    .faq-page a {
        color: teal;
        text-decoration: underline;
    }

    .faq-page a:hover {
        color: #004d4d;
    }

    .justify-content-center {
        display: flex;
        justify-content: center;
    }
</style>

<?php
include 'footer.php'; // Include the footer
?>
