%skip   space           [\s\t\n\r]

%token  built_in        (int|string|float|bool|object|iterable|mixed|numeric-string|boolean|integer|double|real|resource|self|static|scalar|numeric|array-key)

%token  array           (array|non-empty-array)
%token  callable        callable

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
%token  id              \\?[a-zA-Z][a-zA-Z0-9_]*(\\[a-zA-Z][a-zA-Z0-9_]+)*

type:
    (basic_type() | derived_type()) (::bracket_:: ::_bracket::)*

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
  | class()

class:
    <id>

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
    <id> ::namespace:: <id>

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
    <id> ::angular_:: type() (::comma:: type())* ::_angular::

closure:
    (<id> | <callable>) ::parenthesis_:: closure_parameters()? ::_parenthesis:: (::colon:: type())?

closure_parameters:
    type() (::comma:: type())*
