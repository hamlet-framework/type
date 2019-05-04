%skip   space           \s

%token  built_in        (integer|int|string|float|boolean|bool|object|iterable|mixed|numeric-string|double|real|resource|self|static|scalar|numeric|array-key)

%token  array           (array|non-empty-array)
%token  closure         (callable|Closure)

%token  false           false
%token  true            true
%token  null            null

%token  quote_          '        -> string
%token  string:string   [^']+
%token  string:_quote   '        -> default


%token  parenthesis_    \(
%token _parenthesis     \)

%token  brace_          \{
%token _brace           \}

%token  bracket_        \[
%token _bracket         \]

%token  angular_        <
%token _angular         >


%token  question        \?
%token  namespace       ::
%token  colon           :
%token  comma           ,
%token  or              \|
%token  and             &
%token  float_number    (\d+)?\.\d+
%token  int_number      \d+
%token  id              [a-zA-Z_][a-zA-Z0-9_]*
%token  backslash       \\

type:
  ( basic_type() | derived_type() ) legacy_array()*

derived_type:
    union()
  | intersection()

basic_type:
    escaped_type()
  | <built_in>
  | literal()
  | array()
  | generic()
  | closure()
  | class_name()

quoted_string:
    ::quote_:: <string> ::_quote::

literal:
    quoted_string()
  | <int_number>
  | <float_number>
  | <true>
  | <false>
  | <null>
  | const()

const:
    class_name() ::namespace:: <id>

array:
    <array> ::brace_:: property() (::comma:: property())* ::_brace::
  | <array> ::angular_:: type() ::comma:: type() ::_angular::
  | <array> ::angular_:: type() ::_angular::
  | <array>

property_name:
    <id>
  | <int_number>
  | quoted_string()

property:
    property_name() (::question::)? ::colon:: type()
  | type()

escaped_type:
    ::parenthesis_:: type() ::_parenthesis::

union:
    basic_type() ::or:: type()

intersection:
    basic_type() ::and:: type()

generic:
    class_name() ::angular_:: type() (::comma:: type())* ::_angular::

closure:
    <closure> ::parenthesis_:: closure_parameters()? ::_parenthesis:: (::colon:: type())?

closure_parameters:
    type() (::comma:: type())*

class_name:
    ::backslash::? <id> (::backslash:: <id>)*

legacy_array:
    ::bracket_:: ::_bracket::
