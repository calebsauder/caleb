include recipes-core/images/core-image-minimal.bb

IMAGE_FSTYPES = "tar.gz"

IMAGE_INSTALL_append += " \
	apache2 \
	curl \
	fbcp \
	fontconfig \
	fontconfig-utils \
	git \
	php \
	php-cli \
	php-modphp \
	tslib-calibrate \
	tslib-tests \
	ttf-bitstream-vera \
	wireless-tools \
	wpa-supplicant \
	wpebackend \
	wpebackend-rdk \
	wpelauncher \
	wpewebkit \
	"

VIRTUAL-RUNTIME_init_manager="busybox"
