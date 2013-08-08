<link href="<?php echo easy2map_get_plugin_url('/css/bootstrap.min.css'); ?>" rel="stylesheet" media="screen">
<link href="<?php echo easy2map_get_plugin_url('/css/bootstrap-wysihtml5.css'); ?>" rel="stylesheet" media="screen">
<script src="<?php echo easy2map_get_plugin_url('/scripts/bootstrap.min.js'); ?>"></script>

<style type="text/css">

    input[type=text], input[type=password] {
        height: 28px !important;
    }

    td .instructions{
        font-size:14px !important;
        text-align:left;
        font-weight:bold;
    }

    .exampleImg{
        border:1px solid #CCCCCC;
    }

</style>

<div style="width:60%;margin-top:10px;margin-left:auto;margin-right:auto;text-align:right">
    <a id="btnBack" href="?page=easy2map&action=viewmaps">Back to Map Manager</a>
</div>

<form id="save-easy2map_key" name="save-easy2map_key" action="" method="post">

    <table style="background-color:#EBEBEB;width:60%;margin-top:10px;margin-left:auto;margin-right:auto;" cellspacing="3" cellpadding="3" class="table table-bordered">
        <tr>
            <td colspan="2" style="font-size:16px;text-align:left;font-weight:bold;">Unlock all the plugin features by upgrading to the Easy2Map Ultimate Version</td>
        </tr>
        <tr><td colspan="2" style="font-size:13px;text-align:left;font-weight:bold;color:#70aa00;">Ultimate Version Features:</td></tr>
        <tr><td style="width:10%;padding:8px;text-align:center;"><img src="<?php echo easy2map_get_plugin_url('/images/tick_small.png'); ?>"/></td>
            <td style="font-weight:bold;">Edit map layout, with a choice of 11 great looking template options</td></tr>
        <tr><td style="width:10%;padding:8px;text-align:center;"><img src="<?php echo easy2map_get_plugin_url('/images/tick_small.png'); ?>"/></td>
            <td style="font-weight:bold;">Display list of markers alongside map (as one of the map template options)</td></tr>
        <tr><td style="width:10%;padding:8px;text-align:center;"><img src="<?php echo easy2map_get_plugin_url('/images/tick_small.png'); ?>"/></td>
            <td style="font-weight:bold;">Administer map type, and maps' individual style elements to your exact requirements</td></tr>
        <tr><td style="width:10%;padding:8px;text-align:center;"><img src="<?php echo easy2map_get_plugin_url('/images/tick_small.png'); ?>"/></td>
            <td style="font-weight:bold;">Easily import and export Easy2Map maps and/or markers</td></tr>

        <?php
        eval(base64_decode('ZXZhbChiYXNlNjRfZGVjb2RlKCdhV1lnS0dsemMyVjBLQ1JmVUU5VFZGc25aV0Z6ZVRKdFlYQmZhMlY1SjEwcElDWW1JR2x6YzJWMEtDUmZVRTlUVkZzbllXTjBhVzl1SjEwcElDWW1JQ1JmVUU5VFZGc25ZV04wYVc5dUoxMGdQVDBnSW5Wd1pHRjBaVjlsWVhONU1tMWhjR3RsZVNJcElIc0tJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tIZHdYM1psY21sbWVWOXViMjVqWlNna1gxQlBVMVJiSjJWaGMza3liV0Z3WDJ0bGVTZGRMQ0FuZFhCa1lYUmxMVzl3ZEdsdmJuTW5LU2tnZXdvS0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDaHpaV3htT2pwbFlYTjVNazFoY0VOdlpHVldZV3hwWkdGMGIzSW9KRjlRVDFOVVd5ZGpiMlJsSjEwcEtTQjdDaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZFhCa1lYUmxYMjl3ZEdsdmJpZ25aV0Z6ZVRKdFlYQXRhMlY1Snl3Z0pGOVFUMU5VV3lkamIyUmxKMTBwT3dvS0NpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaV05vYnlBblBIUnlQangwWkNCamIyeHpjR0Z1UFNJeUlpQnpkSGxzWlQwaWRHVjRkQzFoYkdsbmJqcGpaVzUwWlhJN2RtVnlkR2xqWVd3dFlXeHBaMjQ2Yldsa1pHeGxPMmhsYVdkb2REbzBNSEI0TzJadmJuUXRjMmw2WlRveExqTmxiVHRqYjJ4dmNqb2pOekJoWVRBd08yWnZiblF0ZDJWcFoyaDBPbUp2YkdRN0lqNEtJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRmx2ZFhJZ2RtVnljMmx2YmlCb1lYTWdjM1ZqWTJWemMyWjFiR3g1SUdKbFpXNGdkWEJuY21Ga1pXUXNJSFJvWVc1cklIbHZkU0VLSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdQQzkwWkQ0OEwzUnlQaWM3Q2lBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0NpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOUlHVnNjMlVnZXdvS0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmxZMmh2SUNjOGRISStQSFJrSUdOdmJITndZVzQ5SWpJaUlITjBlV3hsUFNKMFpYaDBMV0ZzYVdkdU9tTmxiblJsY2p0MlpYSjBhV05oYkMxaGJHbG5ianB0YVdSa2JHVTdhR1ZwWjJoME9qUXdjSGc3Wm05dWRDMXphWHBsT2pFdU0yVnRPMk52Ykc5eU9uSmxaRHRtYjI1MExYZGxhV2RvZERwaWIyeGtPeUkrQ2lBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCWmIzVnlJSFpsY25OcGIyNGdkWEJuY21Ga1pTQjNZWE1nYm05MElITjFZMk5sYzNObWRXd2hDaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lEd3ZkR1ErUEM5MGNqNG5Pd29nSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmUW9nSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdDaUFnSUNBZ0lDQWdJQ0FnSUgwZ1pXeHpaU0I3Q2lBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JsWTJodklDYzhkSEkrUEhSa0lHTnZiSE53WVc0OUlqSWlJSE4wZVd4bFBTSjBaWGgwTFdGc2FXZHVPbU5sYm5SbGNqdDJaWEowYVdOaGJDMWhiR2xuYmpwdGFXUmtiR1U3YUdWcFoyaDBPalF3Y0hnN1ptOXVkQzF6YVhwbE9qRXVNMlZ0TzJOdmJHOXlPbkpsWkR0bWIyNTBMWGRsYVdkb2REcGliMnhrT3lJK0NpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUZWd1pHRjBaU0JtWVdsc1pXUWhDaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnUEM5MFpENDhMM1J5UGljN0NpQWdJQ0FnSUNBZ0lDQWdJSDBLSUNBZ0lDQWdJQ0I5Q2lBZ0lDQWdJQ0FnQ2lBZ0lDQWdJQ0FnYVdZZ0tITmxiR1k2T21WaGMza3lUV0Z3UTI5a1pWWmhiR2xrWVhSdmNpaG5aWFJmYjNCMGFXOXVLQ2RsWVhONU1tMWhjQzFyWlhrbktTa2dQVDA5SUdaaGJITmxLU0I3Q2dvZ0lDQWdJQ0FnSUNBZ0lDQmxZMmh2SUNjOGRISStQSFJrSUdOdmJITndZVzQ5SWpJaUlITjBlV3hsUFNKMFpYaDBMV0ZzYVdkdU9tTmxiblJsY2p0MlpYSjBhV05oYkMxaGJHbG5ianB0YVdSa2JHVTdhR1ZwWjJoME9qRXdNSEI0T3lJK0NpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdQR0VnZEdGeVoyVjBQU0pmWW14aGJtc2lJR2h5WldZOUltaDBkSEE2THk5bFlYTjVNbTFoY0M1amIyMHZjR0Y1YldWdWRDOXdZWGx3WVd3dlpXRnplVEpOWVhCUWNtOHVjR2h3SWlCemRIbHNaVDBpWm05dWRDMXphWHBsT2pFdU0yVnRPMk52Ykc5eU9pTTNNR0ZoTURBN1ptOXVkQzEzWldsbmFIUTZZbTlzWkRzaVBrTnNhV05ySUVobGNtVWdkRzhnVlhCbmNtRmtaU0IwYnlCMGFHVWdWV3gwYVcxaGRHVWdWbVZ5YzJsdmJpQm1iM0lnSkRFeExqazVQQzloUGdvZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lEeHBiV2NnYzNSNWJHVTlJbTFoY21kcGJpMXNaV1owT2pNd2NIZzdiV0Z5WjJsdUxYSnBaMmgwT21GMWRHODdJaUJ6Y21NOUlpY2dMaUJsWVhONU1tMWhjRjluWlhSZmNHeDFaMmx1WDNWeWJDZ25MMmx0WVdkbGN5OXdZWGx3WVd3dGRtVnlhV1pwWldRdWNHNW5KeWtnTGlBbklqNDhMM1JrUGp3dmRISStKenNLSUNBZ0lDQWdJQ0I5Q2dvZ0lDQWdJQ0FnSUdWamFHOGdKend2ZEdGaWJHVStKenNLQ2lBZ0lDQWdJQ0FnYVdZZ0tITmxiR1k2T21WaGMza3lUV0Z3UTI5a1pWWmhiR2xrWVhSdmNpaG5aWFJmYjNCMGFXOXVLQ2RsWVhONU1tMWhjQzFyWlhrbktTa2dQVDA5SUdaaGJITmxLU0I3Q2dvZ0lDQWdJQ0FnSUNBZ0lDQmxZMmh2SUNjOGFEVStVR3hsWVhObElFVnVkR1Z5SUZsdmRYSWdRV04wYVhaaGRHbHZiaUJEYjJSbElFaGxjbVU2UEM5b05UNEtJQ0FnSUNBZ0lDQThhVzV3ZFhRZ2RIbHdaVDBpZEdWNGRDSWdibUZ0WlQwaVkyOWtaU0lnYzNSNWJHVTlJbTFoY21kcGJpMXNaV1owT21GMWRHODdiV0Z5WjJsdUxYSnBaMmgwT21GMWRHODdkMmxrZEdnNk9EQWxJaUIyWVd4MVpUMGlJaUF2UGdvZ0lDQWdJQ0FnSUR4aWNqNG5Pd29nSUNBZ0lDQWdJQ0FnSUNCM2NGOXViMjVqWlY5bWFXVnNaQ2duZFhCa1lYUmxMVzl3ZEdsdmJuTW5MQ0FuWldGemVUSnRZWEJmYTJWNUp5azdDaUFnSUNBZ0lDQWdJQ0FnSUdWamFHOGdKenhwYm5CMWRDQjBlWEJsUFNKb2FXUmtaVzRpSUc1aGJXVTlJbUZqZEdsdmJpSWdkbUZzZFdVOUluVndaR0YwWlY5bFlYTjVNbTFoY0d0bGVTSStDaUFnSUNBZ0lDQWdQR2x1Y0hWMElIUjVjR1U5SW5OMVltMXBkQ0lnWTJ4aGMzTTlJbUowYmlCaWRHNHRjSEpwYldGeWVTSWdjM1I1YkdVOUltMWhjbWRwYmkxc1pXWjBPakV3TUhCNE8zZHBaSFJvT2pFMU1IQjRJaUIyWVd4MVpUMGlRV04wYVhaaGRHVWlQaWM3Q2lBZ0lDQWdJQ0FnZlE9PScpKTs='));
        ?>

        <table style="background-color:#EBEBEB;margin-top:40px;margin-left:auto;margin-right:auto;" cellspacing="3" cellpadding="3" class="table table-bordered">
            <tr>
                <td colspan="2" style="font-size:16px;text-align:left;font-weight:bold;">Easy2Map Ultimate Version</td>
            </tr>
            <tr><td colspan="2" style="font-weight:bold;">Map Dashboard</td></tr>
            <tr><td colspan="2" style="text-align:center"><img class="exampleImg" src="<?php echo easy2map_get_plugin_url('/images/ultimate_img1.png'); ?>" width="800px" /></td></tr>
            <tr><td colspan="2" style="font-weight:bold;">Examples of Maps Created in Easy2Map Ultimate Version</td></tr>
            <tr>
                <td style="text-align:center;font-weight:bold;">Map Style 5 (Map Type: Satellite)<br><img class="exampleImg" src="<?php echo easy2map_get_plugin_url('/images/ultimate_img2.png'); ?>" width="400px" /></td>
                <td style="text-align:center;font-weight:bold;">Map Style 11 (Map Type: RoadMap)<br><img class="exampleImg" src="<?php echo easy2map_get_plugin_url('/images/ultimate_img3.png'); ?>" width="400px" /></td>
            </tr>
            <tr>
                <td style="text-align:center;font-weight:bold;">Map Style 3  (Map Type: Terrain)<br><img class="exampleImg" src="<?php echo easy2map_get_plugin_url('/images/ultimate_img4.png'); ?>" width="400px" /></td>
                <td style="text-align:center;font-weight:bold;">Map Style 9  (Map Type: RoadMap)<br><img class="exampleImg" src="<?php echo easy2map_get_plugin_url('/images/ultimate_img5.png'); ?>" width="400px" /></td>
            </tr>
            <tr>
                <td style="text-align:center;font-weight:bold;">Map Style 7 (Map Type: RoadMap)<br><img class="exampleImg" src="<?php echo easy2map_get_plugin_url('/images/ultimate_img6.png'); ?>" width="400px" /></td>
                <td style="text-align:center;font-weight:bold;"></td>
            </tr>




            </form>
