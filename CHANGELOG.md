# Change Log

## 1.3.1 - 2016-07-15

### Fixed

- FullHttpMessageFormatter will not read from streams that you cannot rewind (non-seekable)
- FullHttpMessageFormatter will not read from the stream if $maxBodyLength is zero
- FullHttpMessageFormatter rewinds streams after they are read.

## 1.3.0 - 2016-07-14

### Added

- FullHttpMessageFormatter to include headers and body in the formatted message

### Fixed
 
- #41: Response builder broke header value


## 1.2.0 - 2016-03-29

### Added

- The RequestMatcher is built after the Symfony RequestMatcher and separates
   scheme, host and path expressions and provides an option to filter on the
   method
- New RequestConditional authentication method using request matchers
- Add automatic basic auth info detection based on the URL

### Changed

- Improved ResponseBuilder

### Deprecated

- RegexRequestMatcher, use RequestMatcher instead
- Matching authenitcation method, use RequestConditional instead


## 1.1.0 - 2016-02-25

### Added

 - Add a request matcher interface and regex implementation
 - Add a callback request matcher implementation
 - Add a ResponseBuilder, to create PSR7 Response from a string

### Fixed

 - Fix casting string on a FilteredStream not filtering the output


## 1.0.0 - 2016-01-27


## 0.2.0 - 2015-12-29

### Added

- Autoregistration of stream filters using Composer autoload
- Cookie
- [Apigen](http://www.apigen.org/) configuration


## 0.1.2 - 2015-12-26

### Added

- Request and response factory bindings

### Fixed

- Chunk filter namespace in Dechunk stream


## 0.1.1 - 2015-12-25

### Added

- Formatter


## 0.1.0 - 2015-12-24

### Added

- Authentication
- Encoding
- Message decorator
- Message factory (Guzzle, Diactoros)
