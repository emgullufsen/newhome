#include <iostream>
#include <vector>
#include <string>
#include "cgicc/Cgicc.h"
#include "cgicc/HTTPHTMLHeader.h"
#include "cgicc/HTMLClasses.h"
#include "cgicc/CgiEnvironment.h"

using namespace std;
using namespace cgicc;

int main(int argc, char **argv)
{
   try {
      Cgicc cgiObj;
      const CgiEnvironment cgiEnv = cgiObj.getEnvironment();
      string remIP = cgiEnv.getRemoteAddr();

      // Send HTTP header
      cout << HTTPHTMLHeader() << endl;

      // Set up the HTML document
      cout << html() << head(title("Barfback your IP")) << endl;
      cout << body() << endl;
      cout << "Your IP is: " << remIP << endl;
      
      // Print out the submitted element
      

      // Close the HTML document
      cout << body() << html();
   }
   catch(exception& e) {
      // handle any errors - omitted for brevity
   }
}