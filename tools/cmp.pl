#!/usr/bin/perl
use strict;
use warnings;
use feature qw/say/;

my $dir1="/app/www/ytb-v0/";
my $dir2="/home/admin/project/tango/";

my @objs = (
		#[$dir1."static", $dir2."ds", "diff -r"],
		#[$dir1."static", $dir2."ds", "Only in"],
		[$dir1."wp-content/plugins/collection/", $dir2."p/collection", "diff -r"],
		[$dir1."wp-content/plugins/collection/", $dir2."p/collection", "Only in"],
		[$dir1."wp-content/themes/collection/", $dir2."t/collection", "diff -r"],
		[$dir1."wp-content/themes/collection/", $dir2."t/collection", "Only in"],
	);

for( my $i = 0; $i < @objs; $i++){
	my $cmd = "diff -r $objs[$i][0] $objs[$i][1] | grep \"$objs[$i][2]\"";
	#my $cmd = "diff -r $objs[$i][0] $objs[$i][1]";
	#print "Command: $cmd\n";
	print `$cmd`;
}

