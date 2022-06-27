import './styles/app.scss';

import './bootstrap';

import $ from 'jquery';
import 'bootstrap';

document.addEventListener('DOMContentLoaded', function() {

    if(typeof username === 'string') 
    {
        fetch('/discover')
        .then(response => {
            // Extract the hub URL from the Link header
            const hubUrl = response.headers.get('Link').match(/<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/)[1];


            // Subscribe to /initializeRound topic
            // All players already joined will need to be displayed before another player joins
            const hubInitRound = new URL(hubUrl, window.origin);
            hubInitRound.searchParams.append('topic', '/initializeRound');
            const eventSourceInitRound = new EventSource(hubInitRound);
            eventSourceInitRound.onmessage = function(event) { 
                console.log("/initializeRound message");
                let player = JSON.parse(event.data);

                // Add joined player(s) to the page
                $('#players_row').html(player.playerTemplate);
            }

            // Subscribe to /round topic
            // All users will be able to see other players join in real time
            const hubRound = new URL(hubUrl, window.origin);
            hubRound.searchParams.append('topic', '/round');
            const eventSourceRound = new EventSource(hubRound);
            eventSourceRound.onmessage = function(event) { 
                console.log("/round message");
                let player = JSON.parse(event.data);

                // Add player to the page
                $('#players_row').append(player.playerTemplate);
            }

            // Subscribe to /roundStatus topic
            // All users will be able to see other players join in real time
            const hunRoundStatus = new URL(hubUrl, window.origin);
            hunRoundStatus.searchParams.append('topic', '/roundStatus');
            const eventSourceRoundStatus = new EventSource(hunRoundStatus);
            eventSourceRoundStatus.onmessage = function(event) { 
                console.log("/roundStatus message");
                let player = JSON.parse(event.data);
                console.log(player);
                // Add player status to the page
                $('#status_player_' + player.username).addClass('border border-danger');
                $('#status_player_' + player.username).text(player.status);
                $('#form_player_' + player.username).children('#number').val(player.numberGuessed)

            }

            // Subscribe to player specific topic
            // For real time guessed number status updates
            const hub = new URL(hubUrl, window.origin);
            if(username !== undefined){
                hub.searchParams.append('topic', '/number' + '/' + username);
                // Subscribe to updates
                const eventSource = new EventSource(hub);
                eventSource.onmessage = function(event) {
                        console.log("/number/ " + username + " message");

                        let player = JSON.parse(event.data);
                        $('#status_player_'+username).addClass('border border-danger');
                        $('#status_player_'+username).text(player.status);
                        
                        if(typeof player.isWinner !== 'undefined' && player.isWinner) {
                            // display the winning splash
                            $('#player_winner_splash').css('display','block');
                        }
                }
            }
    
        });

        $("#button_join_game_"+username).on('click', function(e) {
            e.preventDefault();

            $.ajax({
                url: '/loadJoinedPlayers',
                type: 'post',
                dataType: 'application/json',
                data:"",
                success: function(data) {
                    console.log(data.content);
                }
            });

            $.ajax({
                    url: '/join',
                    type: 'post',
                    dataType: 'application/json',
                    data:"",
                    success: function(data) {
                        console.log(data.content);
                    }
            });

        });
    }
});

global.$ = $;
global.guess = function (u) {
    $.ajax({
            url: '/guess',
            type: 'post',
            dataType: 'application/json',
            data: $("#form_player_"+u).serialize(),
            success: function(data) {
                // ... do something with the data...
                console.log(data);
            }
    });
};

$('#exampleModal').show();