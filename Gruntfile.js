/**
 * Created by Florian Degenhardt on 06.05.2015.
 */

//Gruntfile
module.exports = function (grunt) {

    // Global Config Gruntfile
    var globalConfig = {
        src: 'public/',
        dest: 'public/',
        nodemodules: 'node_modules/'
    };

    //Initializing the configuration object
    grunt.initConfig({
        //Global Config
        globalConfig: globalConfig,
        //Import Configs
        pkg: grunt.file.readJSON('package.json'),

        // Task configuration
        concat: {
            options: {
                separator: ';'
            },
            js_lib: {
                src: [
                    './bower_components/jquery/dist/jquery.js',
                    './bower_components/jquery-validate/dist/jquery.validate.js',
                    './bower_components/bootstrap/dist/js/bootstrap.js',
                    './bower_components/bootstrap3-dialog/dist/js/bootstrap-dialog.js',
                    './bower_components/matchmedia/matchMedia.js',
                    //'./bower_components/jquery.selectBoxIt.js/src/javascripts/jquery.selectBoxIt.js',
                    './bower_components/jquery-placeholder/jquery.placeholder.js',
                    './bower_components/bootstrap-select/dist/js/bootstrap-select.js',
                    './bower_components/jquery-touchswipe/jquery.touchSwipe.js',
                    './bower_components/SocialShare/SocialShare.min.js',
                    './bower_components/wow/dist/wow.min.js',
                    //'./bower_components/webfontloader/webfontloader.js',
                    //'<%= globalConfig.src %>js/lib/**/*.js'
                ],
                dest: '<%= globalConfig.dest %>js/lib.js'
            },
            js_frontend: {
                src: [
                    '<%= globalConfig.src %>js/foreignxperts/**/*.js'
                ],
                dest: '<%= globalConfig.dest %>js/frontend.js'
            }
        },
        less: {
            compile_frontend: {
                options: {
                    compress: false,
                    yuicompress: false,
                    dumpLineNumbers: 'comments',
                    optimization: 2
                },
                files: {
                    // target.css file: source.less file
                    "<%= globalConfig.dest %>css/bootstrap.css": "<%= globalConfig.src %>css/bootstrap.less",
                    "<%= globalConfig.dest %>css/bootstrap-theme.css": "<%= globalConfig.src %>css/bootstrap-theme.less"
                }
            },
            compress_frontend: {
                options: {
                    compress: true,
                    yuicompress: true,
                    optimization: 2
                },

                files: {
                    // target.css file: source.less file
                    "<%= globalConfig.dest %>css/bootstrap.min.css": "<%= globalConfig.src %>css/bootstrap.less",
                    "<%= globalConfig.dest %>css/bootstrap-theme.min.css": "<%= globalConfig.src %>css/bootstrap-theme.less"
                }
            }
        },
        copy: {
            // copy font files
            fonts: {
                files: [
                    // flattens results to a single level
                    {
                        expand: true,
                        flatten: true,
                        src: ['bower_components/fontawesome/fonts/**', 'bower_components/bootstrap/dist/fonts/**'],
                        dest: '<%= globalConfig.dest %>fonts/',
                        filter: 'isFile'
                    }
                ]
            }
        },
        //Packed Sources
        uglify: {
            frontend: {
                files: {
                    '<%= globalConfig.dest %>js/bootstrap.min.js': ['<%= globalConfig.dest %>js/lib.js', '<%= globalConfig.dest %>js/frontend.js']
                }
            }
        },
        //phpunit{
        //...
        //},
        watch: {
            options: {
                interrupt: true,
                nospawn: false,
                debounceDelay: 250
            },
            less_frontend: {
                files: ["<%= globalConfig.src %>css/**/*.less"], // which files to watch
                tasks: ['less:compile_frontend', 'postcss']
            },
            js_lib: {
                files: ['./bower_components/**/*.js', '<%= globalConfig.src %>js/lib/**/*.js'],  //watched files
                tasks: ['concat:js_lib']     //tasks to run
            },
            js_frontend: {
                files: ['<%= globalConfig.src %>js/foreignxperts/**/*.js'],  //watched files
                tasks: ['concat:js_frontend'],     //tasks to run
                options: {
                    livereload: true                        //reloads the browser
                }
            }
        },
        // PNG optimization
        pngquant: {
            dist: {
                options: {
                    binary: "<%= globalConfig.nodemodules %>pngquant-bin/vendor/pngquant"
                },
                src: ["<%= globalConfig.src %>images/**/*.png"]
            }

        },
        imagemin: {                         // Task
            dynamic: {                      // target
                options: {                  // Target options
                    optimizationLevel: 3,   // for PNG, so not used right now
                    progressive: true       //lossless conversion to progressive JPGs
                },
                files: [{
                    expand: true,                           // Enable dynamic expansion
                    cwd: '<%= globalConfig.src %>images/',  // Src matches are relative to this path
                    src: ['**/*.{jpg,gif}'],                // Actual patterns to match
                    dest: '<%= globalConfig.src %>images/'  // Destination path prefix
                }]
            }
        },
        image: {
            static: {
                options: {
                    pngquant: true,
                    optipng: true,
                    advpng: true,
                    zopflipng: true,
                    pngcrush: true,
                    pngout: true,
                    mozjpeg: true,
                    jpegRecompress: true,
                    jpegoptim: true,
                    gifsicle: true,
                    svgo: true
                }
            },
            dynamic: {
                options: {
                    pngquant: true,
                    optipng: true,
                    advpng: true,
                    zopflipng: true,
                    pngcrush: true,
                    pngout: true,
                    mozjpeg: true,
                    jpegRecompress: true,
                    jpegoptim: true,
                    gifsicle: true,
                    svgo: true
                },
                files: [{
                    expand: true,
                    cwd: '<%= globalConfig.src %>images/',
                    src: ['**/*.{png,jpg,gif,svg}'],
                    dest: '<%= globalConfig.src %>images/'
                }]
            }
        },
        postcss: {
            options: {
                map: false, // inline sourcemaps

                processors: [
                    require('autoprefixer-core')({browsers: 'last 2 versions'}) // add vendor prefixes
                ]
            },
            dist: {
                src: '<%= globalConfig.src %>css/*.css'
            }
        }
    });

    //Watch
    grunt.event.on('watch', function (action, filepath, target) {
        grunt.log.writeln(target + ': ' + filepath + ' has ' + action);
    });

    // // Plugin loading
    //grunt.loadNpmTasks('grunt-env');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-pngquant-preserve');
    //grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-image');
    grunt.loadNpmTasks('grunt-postcss');

    //grunt.loadNpmTasks('grunt-phpunit');

    // Task definition
    grunt.registerTask('optimizeImages', ['pngquant', 'image']);
    grunt.registerTask('watchChanges', ['watch']);
    //grunt.registerTask('build', ['install', 'copy:fonts', 'less:compile_frontend', 'postcss', 'less:compress_frontend', 'concat:js_lib', 'concat:js_frontend', 'uglify:frontend']);
    grunt.registerTask('build-dev', ['install', 'copy:fonts', 'less:compile_frontend', 'postcss', 'concat:js_lib', 'concat:js_frontend']);
    grunt.registerTask('build', ['build-dev', 'less:compress_frontend', 'uglify:frontend']);

    grunt.registerTask('install', 'install the backend and frontend dependencies', function () {
        var exec = require('child_process').exec;
        var cb = this.async();
        exec('bower install', {cwd: './'}, function (err, stdout, stderr) {
            console.log(stdout);
            cb();
        });
    });

};
