<script type="text/javascript"
     src="http://api.solvemedia.com/papi/challenge.script?k=<?php echo $challenge_key; ?>">
  </script>

  <noscript>
     <iframe src="http://api.solvemedia.com/papi/challenge.noscript?k=<?php echo $challenge_key; ?>"
  	 height="300" width="500" frameborder="0"></iframe><br/>
     <textarea name="adcopy_challenge" rows="3" cols="40">
     </textarea>
     <input type="hidden" name="adcopy_response"
  	 value="manual_challenge"/>
  </noscript>
