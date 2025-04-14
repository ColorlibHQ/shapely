
# Shapley WordPress Theme Repository Clone & Setup Guide

## About Shapely

Shapely is a powerful and versatile one-page WordPress theme with pixel-perfect design and outstanding functionality. It is by far the most advanced free WordPress theme available today with loads of unmatched customization options. This theme comes with several homepage widgets that can be used to add portfolio, testimonials, parallax sections, your product or service information, call for action and much more.

Shapely supports most free and premium WordPress plugins such as WooCommerce, Jetpack, Gravity Forms, Kali Forms, Contact Form 7, Yoast SEO, Google Analytics by Yoast and much more.

This theme is best suited for business, landing pages, portfolios, e-commerce, store, local business, and personal websites but can be tweaked to be used as a blog, magazine or any other awesome website while highlighting its unique one-page setup. This is going to be the last WordPress theme you will ever want to use because it is so much better than anything you have seen. We promise.

## Git Submodule Information

This repository contains Git submodules. Submodules allow external repositories to be embedded within this repository while maintaining their separate version history. This means:

- The repository cannot be completely downloaded using GitHub's web interface
- Special clone commands are required to properly set up all dependencies
- Additional steps are needed when pulling updates

## Clone Options

### Option 1: Clone with Submodules (Recommended)

The simplest way to clone this repository with all its submodules in one command:

```bash
git clone --recurse-submodules https://github.com/puikinsh/shapely.git
```

For older Git versions (before 1.9), use:

```bash
git clone --recursive https://github.com/puikinsh/shapely.git
```

### Option 2: Standard Clone + Initialize Submodules

If you've already cloned the repository without the special flags:

```bash
# First, clone the repository normally
git clone https://github.com/puikinsh/shapely.git
cd shapely

# Then initialize and update the submodules
git submodule init
git submodule update
```

### Option 3: Using GUI Tools (Windows)

For Windows users preferring a graphical interface:

1. Download and install [TortoiseGit](https://tortoisegit.org/)
2. Right-click in the directory where you want to clone
3. Select "Git Clone..."
4. Enter the repository URL
5. Make sure "Recursive" is checked
6. Click OK

## Working with the Repository

### Keeping Submodules Updated

When pulling updates from the main repository, submodules need to be updated separately:

```bash
# Pull updates from main repo
git pull

# Update submodules to match
git submodule update --init --recursive
```

### Making Changes to Submodules

If you need to modify a submodule:

1. Navigate to the submodule directory
2. Make your changes
3. Commit and push within the submodule
4. Return to the main repository
5. Commit the updated reference to the submodule

```bash
# Example workflow
cd [submodule-directory]
# Make changes to files
git add .
git commit -m "Update submodule"
git push

# Return to main repo and update reference
cd ..
git add [submodule-directory]
git commit -m "Update submodule reference"
git push
```

### Troubleshooting Empty Submodule Directories

If you notice empty directories where submodules should be:

```bash
git submodule update --init --recursive
```

## Setup Script

For your convenience, we've included a setup script to automate the initialization process:

```bash
# For Linux/Mac
./setup.sh

# For Windows
setup.bat
```

This script will:
1. Check if all submodules are properly initialized
2. Initialize any missing submodules
3. Pull the latest versions of submodules (based on the commit references)
4. Verify the setup is complete

## Development Workflow

After cloning and setting up the repository:

1. **WordPress Environment**: Set up a local WordPress development environment
2. **Theme Installation**: Copy or symlink the Shapely directory to your WordPress themes folder
3. **Theme Activation**: Activate the theme through the WordPress admin panel
4. **Development**: Make changes to theme files as needed
5. **Testing**: Test your changes across multiple devices and browsers

## Contributing

We welcome contributions to Shapely! If you'd like to contribute:

1. Fork the repository (including submodules)
2. Create a feature branch
3. Make your changes
4. Submit a pull request

Please ensure all submodules are properly referenced in your pull request.

## Documentation

Complete theme documentation is available [here](https://colorlib.com/wp/themes/shapely/).

## License

This theme is licensed under GPLv3. You can feel free to modify it as long as you keep the original copyright information.

[![Build Status](https://travis-ci.org/puikinsh/shapely.svg?branch=master)](https://travis-ci.org/puikinsh/shapely)
