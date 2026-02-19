ARG BASE_IMAGE
FROM $BASE_IMAGE

USER root

# Install Bun and copy the binary to /usr/local/bin (accessible to all users).
# Symlink to /root/.bun would fail because /root has 700 permissions (www-data blocked).
RUN curl -fsSL https://bun.sh/install | bash \
    && install -m 755 /root/.bun/bin/bun /usr/local/bin/bun \
    && ln -sf /usr/local/bin/bun /usr/local/bin/bunx

ENV BUN_INSTALL="/root/.bun"
ENV PATH="$BUN_INSTALL/bin:$PATH"
