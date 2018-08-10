/** @param {jQuery} $ jQuery Object */
!function($, window, document, _undefined)
{
	"use strict";

	// ################################## LIKE HANDLER ###########################################

	XF.VoteClick = XF.Click.newHandler({
		eventNameSpace: 'XFLikeClick',
		options: {
			voteThread: null,
			threadId: null,
		},

		processing: false,

		init: function()
		{
		},

		click: function(e)
		{
			e.preventDefault();

			if (this.processing)
			{
				return;
			}
			this.processing = true;

			var href = this.$target.attr('href'),
				self = this;

			XF.ajax('POST', href, {}, XF.proxy(this, 'handleAjax'), {skipDefaultSuccess: true})
				.always(function()
				{
					setTimeout(function()
					{
						self.processing = false;
					}, 250);
				});
		},

		handleAjax: function(data)
		{
			var $target = this.$target;

			if (data.addClass)
			{
				$target.addClass(data.addClass);
			}
			if (data.removeClass)
			{
				$target.removeClass(data.removeClass);
			}
			if (data.text)
			{
				var $voteThread = this.options.voteThread ? XF.findRelativeIf(this.options.voteThread, $target) : $([]);

				var $ratingCounter = $voteThread.find('.js-vote-rating');

				var $votesButton = $('.js-vote-like-button-'+this.options.threadId);
				var $unvotesButton = $('.js-vote-unlike-button-'+this.options.threadId);
				var $votesCounter = $('.js-vote-likes-'+this.options.threadId);
				var $unvotesCounter = $('.js-vote-dislikes-'+this.options.threadId);

				var votes = parseInt($votesCounter.text());
				var unvotes = parseInt($unvotesCounter.text());

				if(data.decreaseLike) 
				{ 
					$votesCounter.text(votes - 1);
					votes = votes - 1;

					$votesButton.removeClass('voted-here');
				}
				
				if(data.increaseLike) 
				{
				 	$votesCounter.text(votes + 1);
				 	votes = votes + 1;

				 	$votesButton.addClass('voted-here');
				}

				if(data.decreaseDislike) 
				{ 
					//data.text = 'Vote'; 
					$unvotesCounter.text(unvotes - 1);
					unvotes = unvotes - 1;

					$unvotesButton.removeClass('voted-here');
				}
				
				if(data.increaseDislike) 
				{
				 	//data.text = 'Voted'; 
				 	$unvotesCounter.text(unvotes + 1);
				 	unvotes = unvotes + 1;

				 	$unvotesButton.addClass('voted-here');
				}

				$ratingCounter.text( votes + unvotes);

				//var $label = $target.find('.label');
				//if (!$label.length)
				//{
				//	$label = $target;
				//}

				//if(!$label.attr('ignore-vote-ajax'))
				//{
				//	$label.text(data.text);
				//}
			}
		}
	});

	XF.Click.register('vote', 'XF.VoteClick');
}
(jQuery, window, document);