        </div>
        <footer id="footer">
        </footer>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="/js/libs/jquery-1.7.1.min.js"><\/script>')</script>

    <script src="/js/plugins.js"></script>
    <script src="/js/script.js"></script>

    <script>
        var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
        (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
        g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
        s.parentNode.insertBefore(g,s)}(document,'script'));
    </script>

    <?php $foot = !isset($foot) ? array() : $foot; ?>
    <?php if (ake('js', $foot)): foreach ($foot['js'] as &$_js): ?>
        <script src="<?php echo $_js; ?>"></script>
    <?php endforeach; endif; ?>
</body>
</html>
