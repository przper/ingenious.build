# Domain
I decided that Invoice will be the AggregateRoot. I created `InvoiceLines` aggregate for `InvoiceLines`. I created `Price`, `Quality` and `Text` ValueObjects. The last one I decided to put in the `Shared` BoundedContext together with `Uuid` ValueObject.

Business rules:
- Price and Quality cannot be negative: I think there is no scenario where they can be negative. I ensure this during creation of the ValueObjects. I'm not sure if "positive" in the README.md files should include 0 or not. I decided to allow creation of Price and Quality with the value of 0.0
- Sending Invoice:
  - I considered creating a `SendInvoicePolicy` with `IsNotEmptySpecification` and `HasFilledLinesSpecification`, but I decided to follow the KISS rule and just use simple methods for logic.
  - The check if the Invoice is valid for sending is tested in the `isFilled` method. Another name I considered is `canBeSend`.
  - I check if the Price and Quality is greater than 0.0 (e.g 0.01 is valid) here.
- Confirming delivery of the Invoice:
  - I went with a `confirmDelivery` method that checks the state of the Invoice and updates its status. 
  - A Listener was setUp on `ResourceDeliveredEvent`. If there is a matching Invoice the Listener will try confirm its delivery and persist the Invoice.

# Application
- I'm more familiar with using `Command` and `Query` for instead of `Facade` to access the BoundedContext but I tried my best with Facade as this is what this Project is using from what I recall.
- I see that there is additional layer: `Api`. I could not find its documentation, but I assume that the object from this layer can be used in outside their module. I put my DTOs and FacadeInterface in API layer, and my concrete Facade implementation in Application layer as was done for Notification module.
- I put my Listener in the Application layer. I had some problems with setting it app, because it was not passed to the `NotificationService`'s Dispatcher even though it was listed in the `php artisan event:list`. I spent few hours trying to make it work with no success. Normally I would ask a collegue for help to avoid being stuck on it. I was surprised because it just starting working before task submittion.

# Presentation
- I put all of my controllers here, even the Create and Send. I don't know if this is correct, because those Controllers do not present the data, there are modyfing the data. I don't know if the project itself has additional Layer for that
