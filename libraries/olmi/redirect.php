<?php // $Id: redirect.inc.php,v 1.1 2010/11/11 09:51:40 nastya Exp $

/*
RFC 2616                        HTTP/1.1                       June 1999

10.3.2 301 Moved Permanently

   The requested resource has been assigned a new permanent URI and any
   future references to this resource SHOULD use one of the returned
   URIs.  Clients with link editing capabilities ought to automatically
   re-link references to the Request-URI to one or more of the new
   references returned by the server, where possible. This response is
   cacheable unless indicated otherwise.

   The new permanent URI SHOULD be given by the Location field in the
   response. Unless the request method was HEAD, the entity of the
   response SHOULD contain a short hypertext note with a hyperlink to
   the new URI(s).

   If the 301 status code is received in response to a request other
   than GET or HEAD, the user agent MUST NOT automatically redirect the
   request unless it can be confirmed by the user, since this might
   change the conditions under which the request was issued.

      Note: When automatically redirecting a POST request after
      receiving a 301 status code, some existing HTTP/1.0 user agents
      will erroneously change it into a GET request.

10.3.3 302 Found

   The requested resource resides temporarily under a different URI.
   Since the redirection might be altered on occasion, the client SHOULD
   continue to use the Request-URI for future requests.  This response
   is only cacheable if indicated by a Cache-Control or Expires header
   field.

   The temporary URI SHOULD be given by the Location field in the
   response. Unless the request method was HEAD, the entity of the
   response SHOULD contain a short hypertext note with a hyperlink to
   the new URI(s).

   If the 302 status code is received in response to a request other
   than GET or HEAD, the user agent MUST NOT automatically redirect the
   request unless it can be confirmed by the user, since this might
   change the conditions under which the request was issued.

      Note: RFC 1945 and RFC 2068 specify that the client is not allowed
      to change the method on the redirected request.  However, most
      existing user agent implementations treat 302 as if it were a 303
      response, performing a GET on the Location field-value regardless
      of the original request method. The status codes 303 and 307 have
      been added for servers that wish to make unambiguously clear which
      kind of reaction is expected of the client.

10.3.4 303 See Other

   The response to the request can be found under a different URI and
   SHOULD be retrieved using a GET method on that resource. This method
   exists primarily to allow the output of a POST-activated script to
   redirect the user agent to a selected resource. The new URI is not a
   substitute reference for the originally requested resource. The 303
   response MUST NOT be cached, but the response to the second
   (redirected) request might be cacheable.

   The different URI SHOULD be given by the Location field in the
   response. Unless the request method was HEAD, the entity of the
   response SHOULD contain a short hypertext note with a hyperlink to
   the new URI(s).

      Note: Many pre-HTTP/1.1 user agents do not understand the 303
      status. When interoperability with such clients is a concern, the
      302 status code may be used instead, since most user agents react
      to a 302 response as described here for 303.
*/

class Redirector {

  function redirect($url) {
    if (!headers_sent() && (Request::isHTTP11() || !Request::isPostMethod())) {
      Redirector::redirectLocation($url);
    }
    else {
      Redirector::redirectMeta($url);
    }
    exit();
  }

  function redirectWaitOnErrors($url) {
    if (!headers_sent()) {
      if (Request::isHTTP11() || !Request::isPostMethod()) {
        Redirector::redirectLocation($url);
      }
      else {
        Redirector::redirectMeta($url, 0);
      }
    }
    else {
      Redirector::redirectMeta($url, 5);
    }
    exit();
  }

  function redirectLocation($url) {
    $url = Redirector::makeCompleteUrl($url);
    if (Request::isHTTP11()) {
      header("HTTP/1.1 303 See Other");
    }
    if (defined('__DEBUG') && defined('__LOGGER') && class_exists('Logger')) {
      Logger::message('redirectLocation('.$url.')');
    }
    header("Location: ".$url);
    if (!Request::isHeadMethod() && !defined('REDIRECT_QUIET')) {
      print "Redirecting to ".Redirector::getHyperlink($url)." ...\n";
    }
  }

  function makeCompleteUrl($url) {
    // check if $url contains protocol
    if (preg_match("/^[a-z]+\:\/\//", $url)) {
      return $url;
    }
    if($_SERVER['SERVER_PORT'] == 443)
      $schema = 'https://';
    else
      $schema = 'http://';
    // check if $url is absolute
    if (substr($url,0,1) == '/') {
      return $schema.Request::getHostName().$url;
    }
    if (preg_match("/^((?:\.*\/)+)(.+)$/", $url, $matches)) {
      //TODO determine protocol (http/https) correctly
      //TODO process movement (..) to parent directories
      //TODO use HTTP_HOST or SERVER_NAME which is present
      return $schema.Request::getHostName().Request::getScriptPathOnly().$matches[2];
    }
    return $schema.Request::getHostName().Request::getScriptPathOnly().$url;
  }

  function redirectMeta($url, $delay = 0) {
    if (defined('__DEBUG') && defined('__LOGGER')) {
      Logger::message('redirectMeta('.$url.')');
    }
    print("<html>\n");
    print("<head>\n");
    print("<meta http-equiv=\"Refresh\" content=\"".$delay."; URL=".$url."\">");
    print("</head>\n");
    print("<body>\n");
    
    print("</body>\n");
    print("</html>\n");
    exit();
  }

  function getHyperlink($url) {
    return "<a href=\"".htmlspecialchars($url)."\">".htmlspecialchars($url)."</a>";
  }

}
?>
