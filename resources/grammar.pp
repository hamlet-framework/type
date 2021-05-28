%skip   space           \s

%token  built_in        (integer|int|string|float|boolean|bool|object|iterable|mixed|numeric-string|double|real|resource|self|static|scalar|numeric|array-key|class-string)

%token  array           (array|non-empty-array|list|non-empty-list)
%token  callable        (callable|Closure)

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
%token  word            [^\s]+

expression:
    type() ::word::*

type:
    basic_type()
  | derived_type()

derived_type:
    union()
  | intersection()

#basic_type:
  ( escaped_type() | <built_in> | literal() | object_like_array() | array() | generic() | callable() | class_name() ) brackets()*

quoted_string:
    ::quote_:: <string> ::_quote::

#literal:
    quoted_string()
  | <int_number>
  | <float_number>
  | <true>
  | <false>
  | <null>
  | const()

#const:
    class_name() ::namespace:: <id>

#array:
    <array> ::angular_:: type() ::comma:: type() ::_angular::
  | <array> ::angular_:: type() ::_angular::
  | <array>

#object_like_array:
    <array> ::brace_:: property() (::comma:: property())* ::_brace::

#property_name:
    <id>
  | <int_number>
  | quoted_string()

#property:
    property_name() property_option()? ::colon:: type()
  | type()

#property_option:
    ::question::

escaped_type:
    ::parenthesis_:: type() ::_parenthesis::

#union:
    basic_type() ::or:: type()

#intersection:
    basic_type() ::and:: type()

#generic:
    class_name() ::angular_:: type() (::comma:: type())* ::_angular::

#callable:
    <callable> ::parenthesis_:: callable_parameters()? ::_parenthesis:: (::colon:: type())?

callable_parameters:
    type() (::comma:: type())*

#class_name:
    backslash()? <id> (backslash() <id>)*

#backslash:
    ::backslash::

#brackets:
    ::bracket_:: ::_bracket::
