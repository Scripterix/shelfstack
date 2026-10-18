<?php
declare(strict_types=1);

require_once __DIR__ . '/shared/page_components.php';
echo display_header("Credits");
echo display_navbar();
?>
<main class="container">
    <h1>Credits page</h1>
    <h3>This page includes all the resources that I used for this assessment.</h3>

    <section>
        <h4>Reference list:</h4>
        <ul>
            <li>
                <div>
                    <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript/contributors.txt" target="_blank"> MDN contributors</a>, 2020. Javascript | MDN. [online] Developer.mozilla.org.
                    Available at:
                    <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript" target="_blank">
                        https://developer.mozilla.org/en-US/docs/Web/JavaScript
                    </a>
                    [Accessed 24 December 2020].
                </div>

            </li>
            <li>
                <div>
                    <a href="https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/contributors.txt" target="_blank"> MDN contributors</a>, 2020. Fetch API - Web Apis | MDN. [online] Developer.mozilla.org.
                    Available at:
                    <a href="https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API" target="_blank">
                        https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API
                    </a>
                    [Accessed 24 December 2020].
                </div>
            </li>
            <li>
                <div>
                    Achour, M., Betz, F., Dovgal, A., Lopes, N., Magnusson, H., Richter, G., Seguy, D. and Vrana, J., 2020. PHP: PHP Manual - Manual. [online] Php.net.
                    Available at:
                    <a href="https://www.php.net/manual/en/" target=" _blank">
                        https://www.php.net/manual/en/
                    </a>
                    [Accessed 24 December 2020].
                </div>
            </li>
            <li>
                <div>
                    W3schools.com. 2020. W3schools Online Web Tutorials. [online]
                    Available at:
                    <a href="https://www.w3schools.com/default.asp" target=" _blank">
                        https://www.w3schools.com/default.asp
                    </a>
                    [Accessed 24 December 2020].
                </div>
            </li>
            <li>
                <div>
                    Boostrap Team, 2020. Bootstrap Documentation. [online] Getbootstrap.com.
                    Available at:
                    <a href="https://getbootstrap.com/docs/4.5/getting-started/introduction/" target="_blank">
                        https://getbootstrap.com/docs/4.5/getting-started/introduction/
                    </a>
                    [Accessed 28 December 2020]
                </div>
            </li>
        </ul>
    </section>

    <section>
        <h4>External libraries for styling</h4>
        <p>
            Please, note that I have been using this library for more than 3 years
            and have prior experience with developing webpages. I hope my code does not look
            copied from somewhere as I may have used some prior knowledge from school and programming courses that I took.
        </p>
        <p>
            I hope this is not academic misconduct, as I asked the tutors about it, and I got
            an affirmative response to use it as the styling is not marked.
            I chose this library over writing my own css, because I already know it and it is easier to make the site look pretty.
        </p>
        <p> It has an MIT license and I wanted to include it here, to clarify that it is free to use and modify both in personal and commercial use.</p>
        <p>The MIT License (MIT) </p>
        <p> Copyright (c) 2011-2020 Twitter, Inc. </p>
        <p> Copyright (c) 2011-2020 The Bootstrap Authors</p>
        <p>
            Permission is hereby granted, free of charge, to any person obtaining a copy
            of this software and associated documentation files (the "Software"), to deal
            in the Software without restriction, including without limitation the rights
            to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
            copies of the Software, and to permit persons to whom the Software is
            furnished to do so, subject to the following conditions:
        </p>
        <p>
            The above copyright notice and this permission notice shall be included in
            all copies or substantial portions of the Software.
        </p>
        <p>
            THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
            IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
            FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
            AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
            LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
            OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
            THE SOFTWARE.
        </p>
        <a href="https://getbootstrap.com/">Bootstrap 4</a>
    </section>


</main>

<?php
echo display_footer();
?>
