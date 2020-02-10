<script type="application/javascript">
  function decryptAndOpen(key) {
    window.location.href = "mailto:" + key.split("").reverse().join("").replace("@@", "@");
  }
  
  function decryptAndReturn(key) {
    return key.split("").reverse().join("").replace("@@", "@");
  }
</script>
