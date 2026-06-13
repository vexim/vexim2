# This is the main password for full read-write access.
# Generate a secure hash using the command: rspamadm pw
password = "$2$.....";

# Optional: Define a separate password for read-only access.
# enable_password = "$2$your_read_only_hash_here";

# Specifies which IP addresses are allowed full access without a password.
secure_ip = [ "127.0.0.1", "::1", ];

# Tells the controller to listen on a local socket (for local tools) and a TCP port for nginx.
bind_socket = "/var/run/rspamd/rspamd_controller.sock mode=0600";
bind_socket = "127.0.0.1:11334";
