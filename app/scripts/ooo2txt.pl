# -----------------------------------------------------------------
# Autor          : F. Labbe
# privat         : fred@frederic-labbe.com
# professionnal  : frederic.labbe@ch-avranches-granville.fr
#                : http://ooo2txt.fr.st/
#                  Modified by mirod <mirod@xmltwig.com>
#                  thanks for your help mirod and your class twig.
#		       F. Labbe
# object         : convert OpenOffice.org file into ascii
# Test           : 
# usage (source) : perl ooo2txt [-f ALL] ooo_file 
# usage (binary) :      ooo2txt [-f ALL] ooo_file 
#
# date           : 21/12/2002
# -----------------------------------------------------------------
#
my $ooo2txt_version = "0.0.6";

# -----------------------------------------------------------------
# todo      :
# balise "table:number-columns-repeated" in sxc file
# 
# -----------------------------------------------------------------
# Update    :
# -f all    : display all fields
# XML::Twig 
# -----------------------------------------------------------------

use Getopt::Std;
use XML::Twig;
use Archive::Zip qw(:ERROR_CODES);
use File::Temp qw/ tempfile/;

# test si un parametre saisie
if (@ARGV < 1)
{
	die <<EOF
=>  ooo2txt $ooo2txt_version
=>  usage (source) : perl ooo2txt [-f all] ooo_file 
=>  usage (binary) :      ooo2txt [-f all] ooo_file 

=>  see http://ooo2txt.fr.st/
EOF
}







my $USAGE= "USAGE: $0 [-f all] ooo_file 
see http://ooo2txt.fr.st/";

my $OOO_XML_CONTENT= 'content.xml';

my %opt;

getopts('nvhe:f:', \%opt);
die "$0 version $ooo2txt_version\n" if( $opt{v});
die $USAGE, "\n" if( $opt{h});
$FIELD_NAME=$opt{f} if( $opt{f});

my $zip  = Archive::Zip->new();

#nom du fichier OOo
my $zip_name = shift(@ARGV);

# test si fichier existe
if (open(CONTROL, $zip_name))
{
        my $status = $zip->read( $zip_name );
        die "Read of $zip_name failed\n" 
	if $status != AZ_OK;

	#Extration du fichier content.xml
	my $file = $zip->memberNamed($OOO_XML_CONTENT) or  die "Can't access data file $OOO_XML_CONTENT in zip.\n";

	my $xml  = tempfile();
	my $status= $file->extractToFileHandle($xml)  and die "Extracting $OOO_XML_CONTENT from $zip_name failed\n";
	seek( $xml, 0, 0);


	my %option;
	$option{output_encoding}= $opt{e} if( $opt{e});

	my $state={}; # various state information used during parsing;

	my $conv= $opt{e} ? XML::Twig::encoding_filter( $opt{e}) : sub { return @_; };

	# test
	# EXTRACT FIELD_NAME ?
	if (length($FIELD_NAME) > 0)
	{

         #my $conv = XML::Twig::unicode_convert('latin1');
	 $t= XML::Twig->new( %option,
	 #                      output_filter=> $conv,
                       twig_roots => { 
	#						     'text:h' => \&h,
	##                                       'text:p' => sub { print $conv->( $_->text), "\n"; },
	## balise   : 'text:text-input' 
	## attribut : 'text:description'
	## extract all INPUT-FIELD.
	       'text:text-input' => sub {  print $conv->($_->att('text:description')),"=",$conv->( $_->text),"\n"; },
	#      'text:text-input' => sub {  print $conv->($_->att('text:description')),"=",$conv->( $_->text),"\n"; },
	#      'text:text-input' => sub {  print $conv->($_->start_tag),$conv->( $_->text), "\n"; },
	                                     },
	                     );
	}
	# DUMP TEXT
	else
	{
	 $t= XML::Twig->new( %option,
	                       twig_roots => {  
					     'text:h' => \&h,
	                                     'text:p' => sub { print $conv->( $_->text), "\n"; },
	                                     },
        	             );
	}



	# Affichage du texte
	$t->parse( $xml);  
}
else
{
print ("
File $zip_name not found.

=>  ooo2txt v$ooo2txt_version
=>  usage (source) : perl ooo2txt [-f all] ooo_file 
=>  usage (binary) :      ooo2txt [-f all] ooo_file 

=>  see http://ooo2txt.fr.st/
");

}


sub h
  { my( $t, $h)= @_;
    my $text= $h->text;
    if( $opt{n})
      { my $text_level= $h->att( "text:level");
        if( $text_level)
          { print "\n";
	    my $number= current_number( $text_level, $state);
            $text= $number . $text;
          }
      } 
    print $conv->( $text), "\n"; 
  }

sub current_number
  { my( $text_level, $state)= @_;
    $state->{text_numbering}||= [];
    my $nb= $state->{text_numbering};
    foreach ( $text_level..@$nb) { pop @$nb; }
    $nb->[$text_level-1]++;
    return join( '.', @$nb) . " "; 
  }
