<?php
header("HTTP/1.0 404 Not Found");
?>
<head>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<body>
    <script>
        $(document).ready(function () {
            //style for best visuallity
            $('#main_wrapper').removeAttr('style').attr('style', 'background-color: white;')
        })
    </script>
    <section class="content-section-a" style="background-color: white; padding-bottom: 0px;">
        <div class="container" style="margin-bottom: 0px !important;">
            <div class="row">
                <div class="col-lg-5 ml-auto">
                    <hr class="section-heading-spacer">
                    <div class="clearfix"></div>
                    <h2 class="section-heading">Error 404<br />
                        Page not Found</h2>
                </div>
                <div class="col-lg-5 mr-auto">
                    <div class="container" style="display: flex; margin-bottom: 0px !important;">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
