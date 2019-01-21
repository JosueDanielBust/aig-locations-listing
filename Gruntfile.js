module.exports =  function(grunt) {
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-sass');

	grunt.initConfig({
		copy: {
			main: {
				expand: true,
				src: ['*.php', 'css/*.css', 'js/*.js', 'images/*.*', 'README.md', 'LICENSE.md'],
				dest: '/Applications/MAMP/htdocs/wordpress/wp-content/plugins/aig-locations-listing/',
			},
			dist: {
				expand: true,
				src: ['*.php', 'css/*.css', 'js/*.js', 'images/*.*', 'README.md', 'LICENSE.md'],
				dest: 'dist/',
			}
		},
		watch: {
			copy: {
				files: ['*.*', 'css/*.css', 'js/*.js'],
				tasks: ['copy:main'],
				options: {
					event: ['all'],
					dateFormat: function(time) {
						grunt.log.writeln(('COPY watch finished in ' + time + 'ms')['cyan']);
						grunt.log.writeln('Waiting for more changes...'['green']);
					},
				},
			},
			sass: {
				files: ['sass/*.scss'],
				tasks: ['sass'],
				options: {
					event: ['all'],
					dateFormat: function(time) {
						grunt.log.writeln(('SASS watch finished in ' + time + 'ms')['cyan']);
						grunt.log.writeln('Waiting for more changes...'['green']);
					},
				},
			}
		},
		sass: {
			dist: {
				options: {
					style: 'compact'
				},
				files: {
					'css/main.css': 'sass/main.scss',
					'css/admin.css': 'sass/admin.scss'
				}
			}
		},
	});

	grunt.registerTask('default', ['watch']);
	grunt.registerTask('dist', ['copy:dist']);
	grunt.registerTask('styles', ['sass']);
};
