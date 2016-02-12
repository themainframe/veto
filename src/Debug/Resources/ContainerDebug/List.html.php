<!DOCTYPE html>
<html>
<head>
    <title>Container Debug</title>
    <link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>
    <style type="text/css">

        body,html {
            font-family: "pt sans", sans-serif;
            margin: 0;
            padding: 20px;
        }

        h1 {
            font-weight: bold;
            margin: 0 0 20px 0;
            font-size: 200%;
        }

        h2 {
            font-size: 120%;
            margin: 0 0 20px 0;
        }

        div.stack {
            font-family: monospace;
            background: #fafafa;
            padding: 20px;
            border: 1px solid #efefef;
            border-radius: 6px;
        }

    </style>
</head>
<body>
<h1>
    Service Container
</h1>
<div class="stack">
    <?php foreach ($services as $name => $service) { ?>
        <strong><?php print $name; ?></strong><br />
        &nbsp; --> <?php print $service->getClassName(); ?>
        <br /><br />
    <?php } ?>
    <?php if (0 === count($services)) { ?>
        <strong>There are no services defined within the container.</strong>
    <?php } ?>
</div>
</body>
</html>