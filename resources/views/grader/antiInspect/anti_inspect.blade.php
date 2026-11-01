<?php
// Get current user's username from auth
$currentUsername = auth()->check() ? auth()->user()->username : null;

// Flag to determine if current user can inspect
$canInspect = false;

// Check if user is logged in
if (!empty($currentUsername)) {
    // Connect to database and check if this user is allowed to inspect
    try {
        // Check if user has group_id 644
        $hasGroup644 = false;
        if (auth()->check()) {
            // $hasGroup644 = auth()->user()->group_id == 644;
        }

        // Get the username from allowed_inspect_users table 
        // Using lowercase comparison for case-insensitive matching
        $allowedUser = DB::table('allowed_inspect_users')
                          ->whereRaw('LOWER(user_id) = ?', [strtolower($currentUsername)])
                          ->where('status', 'E')
                          ->first();
        
        // If record exists or user has group_id 644, user can inspect
        $canInspect = ($allowedUser !== null || $hasGroup644);
    } catch (\Exception $e) {
        // If there's any error, default to not allowing inspection
        $canInspect = false;
    }
}
?>
<!-- Anti-inspect CSS -->
<style>
    /* No anti-text-selection styles */
</style>

<script>
    var url = '{{ url('/') }}';
    
    // User inspection permission flag
    var canInspect = <?php echo json_encode($canInspect); ?>;
    
    // Advanced DevTools Detection and Prevention System
    (function() {
        // If user is allowed to inspect, don't apply any restrictions
        if (canInspect) {
            console.log("%c\n   ██████╗ ███╗   ██╗██╗   ██╗    ███████╗██╗   ██╗███████╗\n   ██╔══██╗████╗  ██║██║   ██║    ██╔════╝╚██╗ ██╔╝██╔════╝\n   ██████╔╝██╔██╗ ██║██║   ██║    ███████╗ ╚████╔╝ ███████╗\n   ██╔══██╗██║╚██╗██║██║   ██║    ╚════██║  ╚██╔╝  ╚════██║\n   ██████╔╝██║ ╚████║╚██████╔╝    ███████║   ██║   ███████║\n   ╚═════╝ ╚═╝  ╚═══╝ ╚═════╝     ╚══════╝   ╚═╝   ╚══════╝\n", "color: #003366; font-size: 12px; font-weight: bold;");
            console.log("%cDEVELOPER ACCESS GRANTED", "color: #0066cc; font-size: 16px; font-weight: bold; font-family: Arial, sans-serif; background-color: #f0f8ff; padding: 4px 8px; border-radius: 4px;");
            console.log("%c--------------------------------------------------------------", "color: #0066cc;");
            console.log("%cUser: " + "<?php echo addslashes($currentUsername); ?>", "color: #333; font-size: 13px; font-family: Arial, sans-serif;");
            console.log("%cPermission Level: Authorized Developer", "color: #333; font-size: 13px; font-family: Arial, sans-serif;");
            console.log("%cAuthorization Note: " + "<?php echo isset($allowedUser->reason) ? addslashes($allowedUser->reason) : 'System Administrator Access'; ?>", "color: #555; font-size: 13px; font-family: Arial, sans-serif;");
            console.log("%cAccess Time: " + new Date().toISOString(), "color: #555; font-size: 13px; font-family: Arial, sans-serif;");
            console.log("%c--------------------------------------------------------------", "color: #0066cc;");
            console.log("%cSystem Information", "color: #333; font-size: 13px; font-weight: bold; font-family: Arial, sans-serif;");
            console.log("%cEnvironment: " + "<?php echo ucfirst(app()->environment()); ?>", "color: #555; font-size: 13px; font-family: Arial, sans-serif;");
            console.log("%cLaravel Version: " + "<?php echo app()->version(); ?>", "color: #555; font-size: 13px; font-family: Arial, sans-serif;");
            console.log("%cPHP Version: " + "<?php echo phpversion(); ?>", "color: #555; font-size: 13px; font-family: Arial, sans-serif;");
            console.log("%c--------------------------------------------------------------", "color: #0066cc;");
            console.log("%cSECURITY NOTICE: This is a restricted development environment. All activities are logged and monitored.", "color: #cc0000; font-size: 12px; font-family: Arial, sans-serif; margin-top: 5px;");
            return;
        }
        
        // Store the original console methods
        var originalConsole = {
            log: console.log,
            warn: console.warn,
            error: console.error,
            info: console.info,
            debug: console.debug
        };
        
        // Variable to track if detection has already been triggered
        var detectionTriggered = false;
        
        // Prevent inspect element and developer tools
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Keyboard shortcuts detection
        document.onkeydown = function(e) {
            // Allow F5 for normal page refresh
            if(e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                return true;
            }
            
            if(e.keyCode == 123) { // F12 key
                if (!detectionTriggered) {
                    detectionTriggered = true;
                    logoutUser();
                }
                return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) { // Ctrl+Shift+I
                if (!detectionTriggered) {
                    detectionTriggered = true;
                    logoutUser();
                }
                return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) { // Ctrl+Shift+J
                if (!detectionTriggered) {
                    detectionTriggered = true;
                    logoutUser();
                }
                return false;
            }
            if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) { // Ctrl+U
                if (!detectionTriggered) {
                    detectionTriggered = true;
                    logoutUser();
                }
                return false;
            }
            if(e.ctrlKey && e.keyCode == 'S'.charCodeAt(0)) { // Ctrl+S
                e.preventDefault();
                return false;
            }
        };
        
        // Window size difference detection with improved thresholds
        function detectDevTools() {
            // More accurate threshold for dev tools detection
            var widthThreshold = window.outerWidth - window.innerWidth > 200;
            var heightThreshold = window.outerHeight - window.innerHeight > 200;
            
            // Check for Firefox and other browsers
            if (widthThreshold || heightThreshold) {
                if (!detectionTriggered) {
                    detectionTriggered = true;
                   // logoutUser();
                }
                return true;
            }
            
            // Check for Chrome/Edge specific detection
            var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
            var isEdge = /Edg/.test(navigator.userAgent);
            
            if ((isChrome || isEdge) && !detectionTriggered) {
                var element = new Image();
                Object.defineProperty(element, 'id', {
                    get: function() {
                        if (!detectionTriggered) {
                            detectionTriggered = true;
                            logoutUser();
                        }
                        return '';
                    }
                });
                console.debug(element);
            }
            
            return false;
        }
        
        // Logout user function
        function logoutUser() {
            // Only execute once
            if (detectionTriggered) {
                // Create a form element
                // var form = document.createElement('form');
                // form.method = 'POST';
                // form.action = url + '/logout';
                // form.style.display = 'none';
                
                // // Add CSRF token
                // var csrfToken = document.createElement('input');
                // csrfToken.type = 'hidden';
                // csrfToken.name = '_token';
                // csrfToken.value = '{{ csrf_token() }}';
                // form.appendChild(csrfToken);
                
                // // Add the form to the document and submit it
                // document.body.appendChild(form);
                // form.submit();
            }
        }
        
        // Override console methods to detect DevTools usage, but with less frequency
        console.log = function() {
            // Only check occasionally to reduce false positives
            if (Math.random() < 0.1) detectDevTools();
            return originalConsole.log.apply(console, arguments);
        };
        console.warn = function() {
            // Only check occasionally to reduce false positives  
            if (Math.random() < 0.1) detectDevTools();
            return originalConsole.warn.apply(console, arguments);
        };
        console.error = function() {
            // Only check occasionally to reduce false positives
            if (Math.random() < 0.1) detectDevTools();
            return originalConsole.error.apply(console, arguments);
        };
        
        // Trap for console.clear()
        Object.defineProperty(console, 'clear', {
            get: function() {
                if (!detectionTriggered) {
                    detectionTriggered = true;
                    logoutUser();
                }
                return function() {};
            }
        });
        
        // Disable view source via iframe
        if (window.self !== window.top) {
            window.top.location.href = window.self.location.href;
        }
        
        // Disable common browser shortcuts but allow normal page usage
        window.addEventListener('keydown', function(e) {
            // Allow F5 key for refreshing
            if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                return true;
            }
            
            // Common shortcuts
            if ((e.ctrlKey && (
                e.key === 'p' || // Print
                e.key === 'd' || // Bookmark
                e.key === 'u'    // View source
            )) || 
            e.key === 'F12') {
                
                // Developer tools shortcuts
                if (e.key === 'F12' || 
                    (e.ctrlKey && e.shiftKey && e.key === 'i') || 
                    (e.ctrlKey && e.shiftKey && e.key === 'j') || 
                    (e.ctrlKey && e.key === 'u')) {
                    
                    if (!detectionTriggered) {
                        detectionTriggered = true;
                        logoutUser();
                    }
                }
                
                e.preventDefault();
                return false;
            }
        }, true);
        
        // Set interval for detection but with reduced frequency
        var detectionInterval = setInterval(function() {
            if (detectionTriggered) {
                clearInterval(detectionInterval);
            } else {
                detectDevTools();
            }
        }, 2000); // Check less frequently (every 2 seconds instead of every 1 second)
        
        // Track our anti-inspect script to prevent automatic refresh on normal DOM changes
        var ourScriptTag = document.currentScript;
        
        // Protect against script removal - only specifically for our anti-inspect script
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.removedNodes.length) {
                    for (var i = 0; i < mutation.removedNodes.length; i++) {
                        // Only reload if our specific script is removed
                        if (mutation.removedNodes[i] === ourScriptTag) {
                            if (!detectionTriggered) {
                                detectionTriggered = true;
                                location.reload();
                            }
                        }
                    }
                }
            });
        });
        
        // Only observe high-level DOM changes for script removal
        if (ourScriptTag && ourScriptTag.parentNode) {
            observer.observe(ourScriptTag.parentNode, {
                childList: true
            });
        }
        
        // Make the current window the "real" window to prevent frame breakers
        window.realWindow = window;
        
        // Attach the logout function to the window object for easy access
        window.logoutUser = logoutUser;
    })();
</script> 