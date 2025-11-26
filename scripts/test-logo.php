&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;title&gt;Test Logo&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;h1&gt;Test Logo Display&lt;/h1&gt;
    
    &lt;h2&gt;Method 1: Direct Path&lt;/h2&gt;
    &lt;img src="assets/images/logo.png" alt="Logo Method 1" style="max-width: 200px; border: 2px solid red;"&gt;
    
    &lt;h2&gt;Method 2: Absolute Path&lt;/h2&gt;
    &lt;img src="/base-duanmau/base-duanmau/assets/images/logo.png" alt="Logo Method 2" style="max-width: 200px; border: 2px solid blue;"&gt;
    
    &lt;h2&gt;Method 3: Full URL&lt;/h2&gt;
    &lt;img src="http://localhost/base-duanmau/base-duanmau/assets/images/logo.png" alt="Logo Method 3" style="max-width: 200px; border: 2px solid green;"&gt;
    
    &lt;h2&gt;File Check&lt;/h2&gt;
    &lt;p&gt;File exists: &lt;?php echo file_exists('assets/images/logo.png') ? 'YES' : 'NO'; ?&gt;&lt;/p&gt;
    &lt;p&gt;Full path: &lt;?php echo realpath('assets/images/logo.png'); ?&gt;&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;
