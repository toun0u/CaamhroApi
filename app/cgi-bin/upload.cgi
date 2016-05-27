#!/usr/bin/perl --

use strict;
use CGI qw(:cgi);
use File::Basename;
CGI::private_tempfiles(0);

################################################################################
##								Version 2
## temporary folder to upload files controls by PHP script
##
################################################################################

################################################################################
## Define Variables
################################################################################
## Get Unique ID Passed from PHP, don't use param['sid'], not functional with progress bar
my $sid = (split(/[&=]/,$ENV{QUERY_STRING}))[1];
$sid =~ s/[^a-zA-Z0-9]//g;

if("$sid" eq ""){
	print "Access denied";
	die();
}

################################################################################
## Define Paths
################################################################################
chdir($ENV{'DOCUMENT_ROOT'});
chdir("..");
my $upload_d = "./data/uploads/";
my $upload_dir = "./data/uploads/".$sid."/";
my $tmp_dir =  "./tmp/";
my $session_dir = $tmp_dir.$sid;

################################################################################
## Define filter file name
################################################################################
my $safe_filename_characters = "a-zA-Z0-9_.-";

################################################################################
## Check Temporary Directory created by PHP with special random fonction
################################################################################
if(-d "$session_dir"){
	chmod 0700, "$session_dir";
} else {
	print "Access denied";
	die();
}

## Define Variable for Upload Size
my $upload_size_file = $session_dir.'/upload_size';
my $upload_finished_file = $session_dir.'/upload_finished';
my $upload_size=0;
$CGI::DISABLE_UPLOADS = 0;
my $max_upload = 1450000000;
$CGI::POST_MAX = $max_upload;

## Debuging Output
my $debug = 0;

sub AddSlashes {
    my $text = shift;
    ## Make sure to do the backslash first!
    $text =~ s/([\\\&\'\"\ \(\)])/\\$1/gi;
    return $text;
}
sub in_array{
 	my ($arr,$search_for) = @_;
 	my %items = map {$_ => 1} @$arr;
 	return (exists($items{$search_for}))?1:0;
}

################################################################################
## Auto Flush
################################################################################
umask(0);
$| = 1;
################################################################################
## Start Processing Upload
################################################################################

print "Content-type: text/html\n\n";

################################################################################
## Check Upload Size
################################################################################
if ($ENV{'CONTENT_LENGTH'}> $max_upload) {
	print "<script type='text/javascript'>parent.cancelUpload('Max Upload Size Exceeded'+$ENV{'CONTENT_LENGTH'});</script>";
}
else {
	################################################################################
	## Create Upload Directory if it does not exist
	################################################################################
	if(-d "$upload_dir"){
		chmod 0700, $upload_dir;
	} else {
		mkdir("$upload_dir", 0700) or die();
	}
	if(-d "$upload_d"){
		chmod 0700, $upload_d;
	} else {
		mkdir("$upload_d", 0700) or die();
	}

	################################################################################
	## Create Temporary Directory
	################################################################################
	if(-d "$tmp_dir"){
		chmod 0700, "$tmp_dir";
	} else {
		mkdir("$tmp_dir", 0700) or die();
	}

	################################################################################
	## Create upload_size File
	################################################################################
	open FLENGTH, ">$upload_size_file";
	$upload_size = $ENV{'CONTENT_LENGTH'};
	print FLENGTH "$upload_size";
	close FLENGTH;
	chmod 0660, "$upload_size_file";

	if(-e  $upload_size_file){ } else {

		die();
	}

	################################################################################
	## Relocate Temporary File Directory
	################################################################################
	if ($TempFile::TMPDIRECTORY) {
		$TempFile::TMPDIRECTORY = $session_dir;
	}
	elsif (
		$CGITempFile::TMPDIRECTORY){  $CGITempFile::TMPDIRECTORY = $session_dir;
	}
	sleep(2);

	################################################################################
	## Process Uploaded File
	################################################################################
	if(-d "$session_dir"){
		my $query = new CGI;
		my $file_name = $query->param("filename");
		$file_name =~ s/.*[\/\\](.*)/$1/;

		# Avoid basename hack
		my ( $name, $path, $extension ) = fileparse ( $file_name, '\..*' );
		$file_name = $name . $extension;
		$file_name =~ tr/ /_/;
		$file_name =~ s/[^$safe_filename_characters]//g;

		if ( $file_name =~ /^([$safe_filename_characters]+)$/ ) {
			$file_name = $1;
		}
		else {
			die "File_name contains invalid characters";
		}

		############################
		## Extensions to exclude
		############################
		my @extensions = ("htaccess", "php");
		my $ext = ($file_name =~ m/([^.]+)$/)[0];
		if(in_array(\@extensions,$ext) eq 0){
			my $upload_file_path = $upload_dir.AddSlashes($file_name);
			my $upload_filehandle = $query->upload("filename");
			#close($upload_filehandle);
			#my $tmp_filename = $query->tmpFileName($upload_filehandle);

			open ( UPLOADFILE, ">$upload_dir$file_name" ) or die "$!";
			binmode UPLOADFILE;
			while ( <$upload_filehandle> ) {
				print UPLOADFILE;
			}
			close UPLOADFILE;
			close($upload_filehandle);
		}

		open FLENGTH, ">$upload_finished_file";
		print FLENGTH "1";
		close FLENGTH;
		chmod 0700, "$upload_finished_file";
	}
}
