<?php

namespace percipiolondon\staff\gql\resolvers\mutations;

use Craft;
use craft\gql\base\ElementMutationResolver;
use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\elements\Request as RequestElement;

class Request extends ElementMutationResolver
{
    protected $immutableAttributes = ['id', 'uid'];

    public function createRequest($source, array $arguments, $context, ResolveInfo $resolveInfo)
    {
        $this->requireSchemaAction('request', 'edit');

        $elementService = Craft::$app->getElements();
        $request = $elementService->createElement(RequestElement::class);
        // Have Craft populate the element’s content
        $request = $this->populateElementWithData($request, $arguments);
        // Save the new element
        $request = $this->saveElement($request);

        if($request->hasErrors()) {
            $validationErrors = [];

            foreach ($request->getFirstErrors() as $attribute => $errorMessage) {
                $validationErrors[] = $errorMessage;
            }

            // Throw a UserError with validation messages if we can’t save
            throw new UserError(implode("\n", $validationErrors));
        }

        // Return the newly-saved element
        return $elementService->getElementById($request->id, RequestElement::class);
    }
}